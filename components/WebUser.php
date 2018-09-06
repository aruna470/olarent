<?php

namespace app\components;

use Yii;
use app\models\RolePermission;
use app\models\Role;
use app\models\Matter;

class WebUser extends \yii\web\User
{

	/**
	 * Check single permission
     * @param String $permissionName Name of ther permission.Ex:User.Create -> Controller.Action
	 * @param mixed $params ?
	 * @param Boolean $allowCaching ?
     * @return Boolean true or false
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        if (Yii::$app->user->identity->isSuperadmin) {
            return true;
        }

        try {
            $rp = RolePermission::findOne(['roleName' => Yii::$app->user->identity->roleName, 'permissionName' => $permissionName]);
            if ($rp) {
                return true;
            }
        } catch (Exception $e) {

        }

        return false;
    }

	/**
	 * Check whether atleast one permission exists
     * @param Array $permissionNames Array of permissions
	 * @param mixed $params ?
	 * @param Boolean $allowCaching ?
     * @return Boolean true or false
     */
    public function canList($permissionNames, $params = [], $allowCaching = true)
    {
        foreach ($permissionNames as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }

        return false;
    }
}
