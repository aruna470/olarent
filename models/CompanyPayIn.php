<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "CompanyWireIn".
 *
 * @property integer $id
 * @property string $wireReference
 * @property string $type
 * @property string $ownerName
 * @property string $ownerAddress
 * @property string $bic
 * @property string $iban
 * @property double $amount
 * @property string $currency
 * @property string $status
 * @property string $mpWalletId
 * @property string $mpUserId
 * @property string $createdAt
 * @property integer $createdById
 * @property string $mpPayInId
 */
class CompanyPayIn extends Base
{
    const SCENARIO_CREATE = 'create';

    // MangoPay wire in payment statuses
    const CREATED = "CREATED";
    const SUCCEEDED = "SUCCEEDED";
    const FAILED = "FAILED";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CompanyPayIn';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            // Common
            [['amount'], 'number', 'on' => [self::SCENARIO_CREATE]],
            [['createdAt'], 'safe'],
            [['createdById'], 'integer', 'on' => [self::SCENARIO_CREATE]],
            [['wireReference', 'bic'], 'string', 'max' => 20, 'on' => [self::SCENARIO_CREATE]],
            [['type', 'mpWalletId', 'mpUserId'], 'string', 'max' => 10, 'on' => [self::SCENARIO_CREATE]],
            [['ownerName'], 'string', 'max' => 30, 'on' => [self::SCENARIO_CREATE]],
            [['ownerAddress'], 'string', 'max' => 60, 'on' => [self::SCENARIO_CREATE]],
            [['iban'], 'string', 'max' => 35, 'on' => [self::SCENARIO_CREATE]],
            [['currency'], 'string', 'max' => 4, 'on' => [self::SCENARIO_CREATE]],
            [['status'], 'string', 'max' => 15, 'on' => [self::SCENARIO_CREATE]],

            // Create
            [['wireReference', 'type', 'ownerName', 'ownerAddress', 'bic', 'iban', 'amount', 'currency',
                'status', 'mpWalletId', 'mpUserId', 'createdAt', 'createdById', 'mpPayInId'], 'required', 'on' => [self::SCENARIO_CREATE]],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'wireReference' => Yii::t('app', 'Wire Reference'),
            'type' => Yii::t('app', 'Type'),
            'ownerName' => Yii::t('app', 'Name'),
            'ownerAddress' => Yii::t('app', 'Address'),
            'bic' => Yii::t('app', 'BIC'),
            'iban' => Yii::t('app', 'IBAN'),
            'amount' => Yii::t('app', 'Amount (' . Yii::$app->params['defCurrency'] . ')'),
            'currency' => Yii::t('app', 'Currency'),
            'status' => Yii::t('app', 'Status'),
            'mpWalletId' => Yii::t('app', 'Mp Wallet ID'),
            'mpUserId' => Yii::t('app', 'Mp User ID'),
            'createdAt' => Yii::t('app', 'Created At'),
            'createdById' => Yii::t('app', 'Created By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'createdById']);
    }

    /**
     * @return CompanyWireIn[] All pending wire in payments
     */
    public static function getPendingPayIns()
    {
        return self::find()
            ->andWhere(['status' => self::CREATED])
            ->all();
    }
}
