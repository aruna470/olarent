<?php

namespace app\models;

use Yii;
use app\models\Base;

/**
 * This is the model class for table "Role".
 *
 * @property string $name
 * @property string $description
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $createdById
 * @property integer $updatedById
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class Role extends Base
{
    const SUPER_ADMIN = 'SuperAdmin';
    const SYSTEM_ADMIN = 'SystemAdmin';

    // To get selected permissions from user
    public $userPermissions = array();
    // To get permissions from db
    public $modelPermissions = array();

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Role';
    }

    public function behaviors()
    {
        return [

        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['userPermissions'], 'required', 'message' => Yii::t('app', 'Please select at least one Permission')],
            [['name'], 'match', 'pattern' => '/^[a-zA-Z][A-Za-z0-9_.]*$/'],
            [['name'], 'unique'],
            [['description'], 'string'],
            [['createdById', 'updatedById'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['createdAt', 'updatedAt', 'userPermissions', 'modelPermissions'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
            'createdById' => Yii::t('app', 'Created By'),
            'updatedById' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'createdById']);
}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updatedById']);
    }

    /**
     * @inheritdoc
     * @return RoleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RoleQuery(get_called_class());
    }
}
