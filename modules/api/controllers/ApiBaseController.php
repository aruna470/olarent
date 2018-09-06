<?php
namespace app\modules\api\controllers;

use Yii;
use app\models\User;
use app\modules\api\components\Messages;
use app\modules\api\components\ApiStatusMessages;
use yii\web\Controller;
use yii\helpers\BaseInflector;

class ApiBaseController extends Controller
{
    const OK = 200;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const INTERNAL_SERVER_ERROR = 500;

    public $user;

    public $statusMessages = array(
        // HTML header messages
        self::OK => 'OK',
        self::BAD_REQUEST => 'Bad Request',
        self::UNAUTHORIZED => 'Unauthorized',
        self::FORBIDDEN => 'Forbidden',
        self::NOT_FOUND => 'Not Found',
        self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
    );

    public function filters()
    {
        return array();
    }

    public function init()
    {
        parent::init();
    }

    /**
     * Override before action of parent controller
     */
    public function beforeAction($action)
    {
        $request = Yii::$app->request;
        //Yii::$app->language = 'en-FR';

        $requestData = Yii::$app->urlManager->parseRequest( $request);
        $curAction = @$requestData[0];
        Yii::$app->appLog->action = $curAction;
        Yii::$app->appLog->uniqid = uniqid();
        Yii::$app->appLog->logType = 2;

        if ($request->isGet) {
            $params = $request->get();
        } else {
            $params = Yii::$app->request->rawBody;
            if (empty($params)) {
                $params = Yii::$app->request->post();
            }
        }

        if ($curAction == 'api/util/s3upload') {
            $params = Yii::$app->request->post();
            if (isset($params['fileData'])) {
                $params['fileData'] = substr($params['fileData'], 0, 100) . '....';
            }
        }

        Yii::$app->appLog->writeLog('Request received.', ['requestUri' => $request->absoluteUrl, 'params' => $params]);

        $headers = Yii::$app->request->headers;
        //Yii::$app->appLog->writeLog("token=" . $headers->get('access-token'));
        $this->authenticate($headers->get('api-key'), $headers->get('api-secret'), $headers->get('access-token'), $curAction);

        return true;
    }

    /**
     * Authenticate request.
     * @param string $apiKey API key
     * @param string $apiSecret API secret
     * @param string $userToken User access token
     * @param string $action Current action
     * @return bool true of false
     */
    private function authenticate($apiKey, $apiSecret, $userToken, $action)
    {
        $isAuthSuccess = true;

        $controllerId = BaseInflector::camelize(Yii::$app->controller->id);
        $actionId = BaseInflector::camelize(Yii::$app->controller->action->id);
        $moduleId = BaseInflector::camelize(Yii::$app->controller->module->id);
        $curAction = "{$moduleId}.{$controllerId}.{$actionId}";

        if ($apiKey == Yii::$app->params['api']['apiKey']
            && $apiSecret == Yii::$app->params['api']['apiSecret']) {
            if (!preg_grep("/{$curAction}/i", Yii::$app->params['api']['guestActions'])) {
                $user = User::find()->where('userToken = :userToken', [':userToken' => $userToken])->one();
                $this->user = $user;
                if (empty($user)) {
                    $isAuthSuccess = false;
                } else {
                    Yii::$app->appLog->username = $user->email;
                }
            }
        } else {
            $isAuthSuccess = false;
            if (preg_grep("/{$curAction}/i", Yii::$app->params['api']['noAuth'])) {
                $isAuthSuccess = true;
            }
        }

        if (!$isAuthSuccess) {
            Yii::$app->appLog->writeLog('Authentication failed');
            $response = Messages::commonStatus(ApiStatusMessages::AUTH_FAILED);
            $this->sendResponse($response);
        }
    }

    /**
     * Send Common response.
     * @param array $responseData Data to be sent
     * @param integer $headerStatus HTTP response status
     * @param string $contentType Content type
     */
    public function sendResponse($responseData = array(), $headerStatus = self::OK, $contentType = 'application/json')
    {
        $statusHeader = 'HTTP/1.1 ' . $headerStatus . ' ' . $this->statusMessages[$headerStatus];
        header($statusHeader);
        header('Content-type: ' . $contentType);

        $jsonMessage = json_encode($responseData);
        echo $jsonMessage;

        Yii::$app->appLog->writeLog('Response sent.', ['params' => $responseData]);
        Yii::$app->end();
    }
}
?>