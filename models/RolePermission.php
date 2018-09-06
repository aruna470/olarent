<?php

namespace app\models;

use Yii;
use app\models\Base;

/**
 * This is the model class for table "RolePermission".
 *
 * @property string $roleName
 * @property string $permissionName
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $createdById
 * @property integer $updatedById
 *
 * @property Permission $permission
 * @property Role $role
 * @property User $createdBy
 * @property User $updatedBy
 */
class RolePermission extends Base
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RolePermission';
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
            [['roleName', 'permissionName'], 'required'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['createdById', 'updatedById'], 'integer'],
            [['roleName', 'permissionName'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'roleName' => Yii::t('app', 'Role Name'),
            'permissionName' => Yii::t('app', 'Permission Name'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
            'createdById' => Yii::t('app', 'Created By ID'),
            'updatedById' => Yii::t('app', 'Updated By ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermission()
    {
        return $this->hasOne(Permission::className(), ['name' => 'permissionName']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['name' => 'roleName']);
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
     * @return RolePermissionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RolePermissionQuery(get_called_class());
    }
}
