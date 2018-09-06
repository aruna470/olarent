<?php

namespace app\models;

use yii\mongodb\Exception;
use app\models\Role;

class Auth extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $firstName;
    public $lastName;
    public $email;
    public $timeZone;
    public $roleName;
    public $type;
    public $status;
    public $authKey;
    public $accessToken;
    public $isEmailConfirmed;
    public $isSuperadmin = false;
    private static $users = [
        '100' => [
            'id' => '100',
            'email' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'email' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        try {
            $user = User::findOne(['id' => $id]);
            if (null != $user) {
                return new static([
                    'id' => $user->id,
                    'username' => $user->username,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'email' => $user->email,
                    'timeZone' => $user->timeZone,
                    'roleName' => $user->roleName,
                    'isSuperadmin' => $user->roleName == Role::SUPER_ADMIN ? true : false,
                ]);
            }
        } catch (Exception $e) {

        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user = User::find()->where(['username' => $username])->one();

        if (null != $user) {
            return new static([
                'id' => $user->id,
                'password' => $user->password,
                'email' => $user->email,
                'type' => $user->type,
                'status' => $user->status,
            ]);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        // $this->password - DB password
        // $password - User input password
        return $this->password === User::getComparingPassword($password, $this->password);
    }
}
