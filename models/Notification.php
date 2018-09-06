<?php

namespace app\models;

use Yii;
use app\modules\api\components\ApiStatusMessages;
use app\models\Base;

/**
 * This is the model class for table "Notification".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $viewStatus
 * @property string $messageCode
 * @property string $params
 *
 * @property User $user
 */
class Notification extends Base
{
    // View statuses
    const STATUS_VIEWED = 1;
    const STATUS_PENDING = 0;

    // Notification codes
    const OWNER_RCV_PROP_REQ = 'OWNER_RCV_PROP_REQ';
    const OWNER_ACPT_PRP_REQ = 'OWNER_ACPT_PRP_REQ';
    const OWNER_ACPT_PRP_REQ_OWNER = 'OWNER_ACPT_PRP_REQ_OWNER';
    const OWNER_REJ_PROP_REQ = 'OWNER_REJ_PROP_REQ';
    const TENANT_MON_PAY_DEB = 'TENANT_MON_PAY_DEB';
    const TENANT_MON_PAY_FAIL = 'TENANT_MON_PAY_FAIL';
    const OWNER_MON_PAY_FAIL = 'OWNER_MON_PAY_FAIL';
    const OWNER_MON_PAY_CRDT = 'OWNER_MON_PAY_CRDT';
    const TENANT_NEXT_PAYMENT = 'TENANT_NEXT_PAYMENT';
    const RCV_REVIEW_REQ = 'RCV_REVIEW_REQ';
    const RCV_REVIEW_FB = 'RCV_REVIEW_FB';
    const CC_EXP = 'CC_EXP';
    const PROP_TERMINATE = 'PROP_TERMINATE';
    const TENANT_CRT_PROP_ONBHF = 'TENANT_CRT_PROP_ONBHF';

    // Validation scenarios
    const SCENARIO_CREATE = 'create';
    const SCENARIO_API_UPDATE = 'apiUpdate';

    public $messages = [];
    public $message;

    public function init()
    {
        $this->messages = [
            self::OWNER_RCV_PROP_REQ => Yii::t('noti', 'You have received a request for your rental {code} from {tenantName}'),
            self::OWNER_ACPT_PRP_REQ => Yii::t('noti', 'Your request for the rental {code} was accepted by owner {ownerName}'),
            self::OWNER_ACPT_PRP_REQ_OWNER => Yii::t('noti', 'You have accepted the request for rental {code} from tenant {tenantName}'),
            self::OWNER_REJ_PROP_REQ => Yii::t('noti', 'Your request for the rental {code} was rejected by the owner {ownerName}'),
            self::TENANT_MON_PAY_DEB => Yii::t('noti', 'Your monthly payment {amount}({currency}) for the rental "{code}" was successful'),
            self::TENANT_MON_PAY_FAIL => Yii::t('noti', 'Your payment {amount}({currency}) for the rental "{code}" has failed'),
            self::OWNER_MON_PAY_FAIL => Yii::t('noti', 'Payment {amount}({currency}) for the rental "{code}" has failed'),
            self::OWNER_MON_PAY_CRDT => Yii::t('noti', 'You have received a {amount}({currency}) payment to the rental "{code}"'),
            self::TENANT_NEXT_PAYMENT => Yii::t('noti', 'Next payment date for the rental {code} will be on {date}'),
            self::RCV_REVIEW_REQ => Yii::t('noti', 'You have received a review request from {senderName}'),
            self::RCV_REVIEW_FB => Yii::t('noti', 'You have received a review  from {senderName}'),
            self::CC_EXP => Yii::t('noti', 'Your credit card is about to expire on {date}. Please remember to add a new one.'),
            self::PROP_TERMINATE => Yii::t('noti', 'This rental "{code}"  terminated'),
            self::TENANT_CRT_PROP_ONBHF => Yii::t('noti', 'You have successfully created the rental for the property of {ownerName}. Code is {code}'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'messageCode', 'params'], 'required', 'on' => [self::SCENARIO_CREATE]],
            [['userId', 'viewStatus'], 'integer', 'on' => [self::SCENARIO_CREATE]],
            [['params'], 'string', 'on' => [self::SCENARIO_CREATE]],
            [['messageCode'], 'string', 'max' => 25, 'on' => [self::SCENARIO_CREATE]],

            // API
            [['userId', 'viewStatus'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_UPDATE]],
            [['userId', 'viewStatus'], 'integer', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_UPDATE]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'viewStatus' => Yii::t('app', 'View Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * Prepare notification message
     * @param string $code Notification code
     * @param array $params Dynamic parameters of the message
     * @return string $message
     */
    public function getMessageByCode($code, $params = [])
    {
        $message = $this->messages[$code];
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }

        return $message;
    }

    /**
     * Add notification
     * @param string $messageCode Notification code
     * @param integer $userId Id of the recipient user
     * @param array $params Dynamic parameters
     */
    public function addNotification($messageCode, $userId, $params = [])
    {
        $model = new Notification();
        $model->scenario = self::SCENARIO_CREATE;

        $model->userId = $userId;
        $model->params = json_encode($params);
        $model->messageCode = $messageCode;
        $model->viewStatus = self::STATUS_PENDING;

        $model->saveModel();
    }
}
