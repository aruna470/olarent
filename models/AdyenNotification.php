<?php

namespace app\models;

use Yii;
use app\models\Base;

/**
 * This is the model class for table "AdyenNotification".
 *
 * @property integer $id
 * @property string $originalReference
 * @property string $reason
 * @property string $merchantAccountCode
 * @property string $eventCode
 * @property integer $success
 * @property string $paymentMethod
 * @property string $currency
 * @property string $pspReference
 * @property string $merchantReference
 * @property string $value
 * @property string $eventDate
 * @property string $createdAt
 */
class AdyenNotification extends Base
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'AdyenNotification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createdAt', 'success', 'live'], 'safe'],
            [['originalReference'], 'string', 'max' => 25],
            [['reason', 'merchantAccountCode', 'eventCode', 'pspReference', 'merchantReference', 'value', 'eventDate'], 'string', 'max' => 30],
            [['paymentMethod', 'currency'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'originalReference' => Yii::t('app', 'Original Reference'),
            'reason' => Yii::t('app', 'Reason'),
            'merchantAccountCode' => Yii::t('app', 'Merchant Account Code'),
            'eventCode' => Yii::t('app', 'Event Code'),
            'success' => Yii::t('app', 'Success'),
            'paymentMethod' => Yii::t('app', 'Payment Method'),
            'currency' => Yii::t('app', 'Currency'),
            'pspReference' => Yii::t('app', 'Psp Reference'),
            'merchantReference' => Yii::t('app', 'Merchant Reference'),
            'value' => Yii::t('app', 'Value'),
            'eventDate' => Yii::t('app', 'Event Date'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }
}
