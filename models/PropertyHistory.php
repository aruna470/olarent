<?php

namespace app\models;

use Yii;
use app\models\Base;

/**
 * This is the model class for table "PropertyHistory".
 *
 * @property integer $id
 * @property integer $tenantUserId
 * @property integer $ownerUserId
 * @property integer $propertyId
 * @property string $fromDate
 * @property string $toDate
 *
 * @property Property $property
 * @property User $tenantUser
 * @property User $ownerUser
 */
class PropertyHistory extends Base
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PropertyHistory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenantUserId', 'ownerUserId', 'propertyId', 'fromDate'], 'required'],
            [['tenantUserId', 'ownerUserId', 'propertyId'], 'integer'],
            [['fromDate', 'toDate'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenantUserId' => Yii::t('app', 'Tenant User ID'),
            'ownerUserId' => Yii::t('app', 'Owner User ID'),
            'propertyId' => Yii::t('app', 'Property ID'),
            'fromDate' => Yii::t('app', 'From Date'),
            'toDate' => Yii::t('app', 'To Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(Property::className(), ['id' => 'propertyId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTenantUser()
    {
        return $this->hasOne(User::className(), ['id' => 'tenantUserId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwnerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'ownerUserId']);
    }

//    public function add($tenantUserId, $ownerUserId, $propertyId, $fromDate, $toDate = null)
//    {
//
//    }
}
