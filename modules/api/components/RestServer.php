<?php
namespace app\modules\api\components;

use yii\base\Component;

class RestServer extends Component
{
    // HTML header statuses
    const OK = 200;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const INTERNAL_SERVER_ERROR = 500;

    // Common error codes
    const SUCCESS = 0;
    const AUTH_FAILED = 9999;

    public $statusMessages = array(
        // HTML header messages
        self::OK => 'OK',
        self::BAD_REQUEST => 'Bad Request',
        self::UNAUTHORIZED => 'Unauthorized',
        self::FORBIDDEN => 'Forbidden',
        self::NOT_FOUND => 'Not Found',
        self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
    );

    public $params;
    public $lastRequestData;
    public $lastResponseData;

    /**
     * Class constructor
     */
    /*public function __construct()
    {
        $this->enableCors();

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->params = $_GET;
                break;

            case 'POST':
                if (strstr($_SERVER['CONTENT_TYPE'], 'application/json')) {
                    $this->params = json_decode(file_get_contents('php://input'), true);
                } else {
                    $this->params = $_POST;
                }
                break;

            case 'PUT':
            case 'DELETE':
                parse_str(file_get_contents('php://input'), $this->params);
                break;
        }

        $this->lastRequestData = json_encode(array('requestUri' => $_SERVER['REQUEST_URI'], 'params' => $this->params));
    }*/

    /**
     * Validate username and password against that comes with request
     * @return boolean $isAuthenticated true if authentication success otherwise false
     */
    public function authenticate()
    {

    }

    /**
     * Enable CORS for cross domain access
     */
    private function enableCors()
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    /**
     * Send Common response.
     * @param Array $responseData Data to be sent
     * @param integer $headerStatus HTTP response status
     * @param string $contentType Content type
     */
//    public function sendResponse($responseData = array(), $headerStatus = self::OK, $contentType = 'application/json')
//    {
//        $statusHeader = 'HTTP/1.1 ' . $headerStatus . ' ' . $this->statusMessages[$headerStatus];
//        header($statusHeader);
//        header('Content-type: ' . $contentType);
//
//        if (!empty($responseData)) {
//
//            $jsonMessage = CJSON::encode($responseData);
//
//            $this->lastResponseData = $jsonMessage;
//
//            echo $jsonMessage;
//        }
//    }
}
?>