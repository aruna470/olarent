<?php

namespace app\models;

use app\components\Image;
use Yii;
use yii\helpers\Html;
use app\models\Base;
use app\models\PropertyHistory;
use app\models\Property;
use app\models\UserReview;
use app\components\Aws;
use app\components\Mp;
use app\modules\api\components\ApiStatusMessages;

/**
 * This is the model class for table "User".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $timeZone
 * @property string $roleName
 * @property integer $type
 * @property integer $status
 * @property string $fbId
 * @property string $fbAccessToken
 * @property string $gplusId
 * @property string $gplusAccessToken
 * @property string $linkedInId
 * @property string $linkedInAccessToken
 * @property string $phone
 * @property string $userToken
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $createdById
 * @property integer $updatedById
 * @property string $bankAccountNo
 * @property string $bankName
 * @property string $profileImage
 * @property string $language
 * @property string $idImage
 * @property string $taxFile
 * @property string $dob
 * @property string $lastAccess
 * @property string $bankAccountName
 * @property string $iban
 * @property string $swift
 * @property string $rating
 * @property string $sysEmail
 * @property string $profDes
 * @property string $companyRegNum
 * @property string $companyType
 * @property string companyName
 */
class User extends Base
{
    // User statuses
    const ACTIVE = 1;
    const INACTIVE = 2;

    // Login types
    const LT_EMAIL = 1;
    const LT_FACEBOOK = 2;
    const LT_LINKEDIN = 3;
    const LT_GOOGLE_PLUS = 4;

    // User types
    const OWNER = 1;
    const TENANT = 2;
    const SYSTEM = 3;

    // Is requested for review
    const R4R_YES = 1;
    const R4R_NO = 2;

    // Validation scenarios
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_API_CREATE = 'apiCreate';
    const SCENARIO_API_UPDATE = 'apiUpdate';
    const SCENARIO_API_AUTH = 'apiAuth';
    const SCENARIO_MY_ACCOUNT = 'myAccount';
    const SCENARIO_CHANGE_PASSWORD = 'changePassword';
    const SCENARIO_API_CHANGE_PASSWORD = 'apiChangePassword';
    const SCENARIO_API_INVITE_TENANT = 'apiInviteTenant';
    const SCENARIO_API_FORGOT_PASSWORD = 'apiForgotPassword';
    const SCENARIO_API_RESET_PASSWORD = 'apiResetPassword';
    const SCENARIO_REG_USER_UPDATE = 'regUserUpdate';
    const SCENARIO_API_ON_BH_CREATE = 'apiOnBehalfCreate';
    const SCENARIO_API_ON_BH_UPDATE = 'apiOnBehalfUpdate';

    // Language codes
    const EN_US = 'en-US'; // English US
    const FR_FR = 'fr-FR'; // French France

    // Profile picture name
    const PROF_PIC_NAME = 'prof_pic_{timestamp}_{random}';

    // ON Behalf of
    const ON_BEHALF_YES = 1; // User created on behalf of owner
    const ON_BEHALF_NO = 0;

    // Company types
    const CT_PERSONAL = 0;
    const CT_REAL_STATE_AGENCY = 1;
    const CT_PROPERTY_MANAGEMENT = 2;
    const CT_BUILDING_MANAGEMENT = 3;

    public $confPassword;
    public $captcha;
    public $oldPassword;
    public $newPassword;
    public $curOldPassword;
    public $formPassword;
    public $loginType;
    public $message;
    public $isRequestedForReview;

    private $_statuses = array(
        self::ACTIVE => 'Active',
        self::INACTIVE => 'Inactive',
    );

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'User';
    }

    public function behaviors()
    {
        return [

        ];
    }

    /**
     * Returns the user statuses.
     * @return array statuses array.
     */
    public function getStatuses()
    {
        return $this->_statuses;
    }

    /**
     * Returns the user types.
     * @return array types array.
     */
    public function getTypes()
    {
        return $this->_types;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Common
            [['sysEmail'], 'email', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['username'], 'unique', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['sysEmail'], 'unique', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['password'], 'string', 'max' => 40, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['username'], 'string', 'max' => 15, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['firstName', 'lastName', 'roleName', 'fbId', 'bankName', 'profileImage', 'idImage', 'taxFile'], 'string', 'max' => 30, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['sysEmail', 'timeZone'], 'string', 'max' => 60, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['fbAccessToken', 'bankAccountNo'], 'string', 'max' => 45, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['phone'], 'string', 'max' => 20, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['language'], 'string', 'max' => 3, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
			[['firstName', 'lastName', 'username'], 'match', 'pattern' => '/^[a-zA-Z]+$/', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_MY_ACCOUNT]],
            [['createdById', 'updatedById'], 'integer', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['type', 'status'], 'integer', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],

            // System user create
            [['firstName', 'lastName', 'username',  'sysEmail', 'roleName', 'timeZone'], 'required', 'on' => [self::SCENARIO_CREATE]],
			[['password', 'confPassword', 'formPassword'], 'required', 'on' => [self::SCENARIO_CREATE]],
            [['formPassword'], 'checkPasswordStrength', 'params' => ['min' => 7, 'allowEmpty' => false], 'on' => [self::SCENARIO_CREATE]],
            [['confPassword'], 'compare', 'compareAttribute' => 'formPassword', 'operator' => '==', 'on' => [self::SCENARIO_CREATE]],

            // System user update
            [['firstName', 'lastName', 'username', 'sysEmail', 'roleName', 'timeZone'], 'required', 'on' => [self::SCENARIO_UPDATE]],
            [['formPassword'], 'checkPasswordStrength', 'params' => ['min' => 7, 'allowEmpty' => true], 'on' => [self::SCENARIO_UPDATE]],
            [['confPassword'], 'compare', 'compareAttribute' => 'formPassword', 'operator' => '==', 'on' => [self::SCENARIO_UPDATE]],

            // system user > myAccountSysUser
            [['firstName', 'lastName', 'username', 'sysEmail', 'roleName', 'timeZone'], 'required', 'on' => 'myAccountSysUser'],

            // myAccount
            [['firstName', 'lastName', 'username', 'sysEmail', 'roleName', 'timeZone'], 'required', 'on' => [self::SCENARIO_MY_ACCOUNT]],

            // changePassword
            [['oldPassword', 'newPassword', 'confPassword'], 'required', 'on' => [self::SCENARIO_CHANGE_PASSWORD]],
            [['oldPassword'], 'compare', 'compareValue' => $this->curOldPassword, 'operator' => '==', 'type' => 'string', 'on' => [self::SCENARIO_CHANGE_PASSWORD]],
            [['newPassword'], 'checkPasswordStrength', 'params' => ['min' => 7, 'allowEmpty' => false], 'on' => [self::SCENARIO_CHANGE_PASSWORD]],
            [['confPassword'], 'compare', 'compareAttribute' => 'newPassword', 'operator' => '==', 'type' => 'string', 'on' => [self::SCENARIO_CHANGE_PASSWORD]],

            // Reg user update
            [['email', 'phone'], 'required', 'on' => [self::SCENARIO_REG_USER_UPDATE]],
            [['phone', 'email'], 'unique', 'on' => [self::SCENARIO_REG_USER_UPDATE]],
            [['email'], 'email', 'on' => [self::SCENARIO_REG_USER_UPDATE]],
            [['phone'], 'match', 'pattern' => '/^\+\d+$/', 'on' => [self::SCENARIO_REG_USER_UPDATE]],

            // Safe
            [['createdAt', 'updatedAt', 'captcha', 'password', 'formPassword', 'type', 'lastAccess'], 'safe'],

            // API - Create/Update
            [['email', 'timeZone', 'type', 'status'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_CREATE]],
            [['email'], 'unique', 'message' => ApiStatusMessages::EMAIL_EXISTS, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['email'], 'email', 'message' => ApiStatusMessages::INVALID_EMAIL, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['fbId', 'linkedInId', 'gplusId'], 'unique', 'message' => ApiStatusMessages::SOCIAL_ACCOUNT_EXISTS, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['phone'], 'unique', 'skipOnEmpty'=>true,  'message' => ApiStatusMessages::PHONE_EXISTS, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['phone'], 'match', 'pattern' => '/^\+\d+$/', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['formPassword', 'linkedInId'], 'string', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'max' => 15, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['gplusId'], 'string', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'max' => 35, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['firstName', 'lastName', 'roleName', 'fbId', 'bankName', 'idImage', 'taxFile', 'swift', 'companyName'], 'string',
                'max' => 30, 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['fbAccessToken', 'gplusAccessToken', 'linkedInAccessToken', 'bankAccountNo'], 'string', 'max' => 45, 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['phone'], 'string', 'max' => 20, 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['companyRegNum'], 'string', 'max' => 15, 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            // This validation removed as some social accounts let to use any characters for name
            //[['firstName', 'lastName', 'username'], 'match', 'pattern' => '/^[a-zA-Z]+$/', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z]+$/', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['dob'], 'match', 'pattern' => '/^\d{4}-\d{2}-\d{2}+$/', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['profileImage', 'profDes'], 'safe', 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['profileImageThumb'], 'safe', 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['bankAccountName', 'bankAccountNo'], 'match', 'pattern' => '/^[a-zA-Z\d_ -]+$/', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['language'], 'isValidLang', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],
            [['companyType'], 'in', 'range' => [self::CT_PERSONAL, self::CT_REAL_STATE_AGENCY, self::CT_BUILDING_MANAGEMENT, self::CT_PROPERTY_MANAGEMENT],
                'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_UPDATE]],

            // API - Create on behalf of user
            [['firstName',  'email', 'timeZone', 'type', 'status'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_ON_BH_CREATE]],
            [['email'], 'email', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_ON_BH_CREATE, self::SCENARIO_API_ON_BH_UPDATE]],
            [['email'], 'unique', 'message' => ApiStatusMessages::EMAIL_EXISTS,
                'on' => [self::SCENARIO_API_ON_BH_CREATE, self::SCENARIO_API_ON_BH_UPDATE]],
            [['firstName', 'lastName', 'bankName', 'swift', 'companyName'], 'string',
                'max' => 30, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_ON_BH_CREATE, self::SCENARIO_API_ON_BH_UPDATE]],
            [['email', 'bankAccountName', 'iban'], 'string', 'max' => 60, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_ON_BH_CREATE, self::SCENARIO_API_ON_BH_UPDATE]],
            [['companyRegNum'], 'string', 'max' => 15, 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_ON_BH_CREATE, self::SCENARIO_API_ON_BH_UPDATE]],

            // API - User authentication
            [['loginType'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_AUTH]],
            [['loginType'], 'checkDependantFields', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_AUTH]],
            [['email', 'fbId', 'gplusId', 'linkedInId', 'password'], 'safe', 'on' => [self::SCENARIO_API_AUTH]],

            // API - Change password
            [['password', 'oldPassword'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_CHANGE_PASSWORD]],
            [['oldPassword'], 'compare', 'compareValue' => $this->curOldPassword, 'message' => ApiStatusMessages::INVALID_OLD_PASSWORD, 'operator' => '==', 'type' => 'string', 'on' => [self::SCENARIO_API_CHANGE_PASSWORD]],

            // API - Invite tenant
            [['message', 'email'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_INVITE_TENANT]],
            [['email'], 'email', 'message' => ApiStatusMessages::INVALID_EMAIL, 'on' => [self::SCENARIO_API_INVITE_TENANT]],

            // API - Forgot password
            [['email'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_FORGOT_PASSWORD]],
            [['email'], 'email', 'message' => ApiStatusMessages::INVALID_EMAIL, 'on' => [self::SCENARIO_API_FORGOT_PASSWORD]],

            // API - Reset password
            [['passwordResetToken', 'password'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD,
                'on' => [self::SCENARIO_API_RESET_PASSWORD]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'formPassword' => Yii::t('app', 'Password'),
            'oldPassword' => Yii::t('app', 'Old Password'),
            'firstName' => Yii::t('app', 'First Name'),
            'lastName' => Yii::t('app', 'Last Name'),
            'email' => Yii::t('app', 'Email'),
            'sysEmail' => Yii::t('app', 'Email'),
            'confPassword' => Yii::t('app', 'Confirm Password'),
            'newPassword' => Yii::t('app', 'New Password'),
            'timeZone' => Yii::t('app', 'Time Zone'),
            'roleName' => Yii::t('app', 'Role'),
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
            'bankName' => Yii::t('app', 'Bank Name'),
            'bankAccountNo' => Yii::t('app', 'Bank Account Number'),
            'idImage' => Yii::t('app', 'National Id'),
            'taxFile' => Yii::t('app', 'Tax File'),
            'dob' => Yii::t('app', 'Date of birth'),
            'lastAccess' => Yii::t('app', 'Last Access Date'),
            'bankAccountName' => Yii::t('app', 'Bank Account Name'),
            'iban' => Yii::t('app', 'IBAN'),
            'swift' => Yii::t('app', 'SWIFT'),
            'rating' => Yii::t('app', 'User Rating'),
            'fbId' => Yii::t('app', 'Facebook Id'),
            'fbAccessToken' => Yii::t('app', 'Facebook Access Token'),
            'gplusId' => Yii::t('app', 'Google+ Id'),
            'gplusAccessToken' => Yii::t('app', 'Google+ Access Token'),
            'linkedInId' => Yii::t('app', 'LinkedIn Id'),
            'linkedInAccessToken' => Yii::t('app', 'LinkedIn Access Token'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
            'createdById' => Yii::t('app', 'Created By Id'),
            'updatedById' => Yii::t('app', 'Updated By Id'),
            'isOnBhf' => Yii::t('app', 'Is On Behalf'),
            'profDes' => Yii::t('app', 'Profile Description'),
        ];
    }

    /**
     * Validate language type
     */
    public function isValidLang()
    {
        $langs = [self::EN_US, self::FR_FR];
        if (!in_array($this->language, $langs)) {
            $this->addError('language', ApiStatusMessages::VALIDATION_FAILED);
        }
    }

    /**
     * Validate required fileds depending on login type
     */
    public function checkDependantFields()
    {
        switch ($this->loginType) {
            case self::LT_EMAIL:
                if ("" == $this->email) {
                    $this->addError('email', ApiStatusMessages::MISSING_MANDATORY_FIELD);
                } else if ("" == $this->password) {
                    $this->addError('password', ApiStatusMessages::MISSING_MANDATORY_FIELD);
                }
                break;

            case self::LT_FACEBOOK:
                if ("" == $this->fbId) {
                    $this->addError('fbId', ApiStatusMessages::MISSING_MANDATORY_FIELD);
                }
                break;

            case self::LT_GOOGLE_PLUS:
                if ("" == $this->gplusId) {
                    $this->addError('gplusId', ApiStatusMessages::MISSING_MANDATORY_FIELD);
                }
                break;

            case self::LT_LINKEDIN:
                if ("" == $this->linkedInId) {
                    $this->addError('linkedInId', ApiStatusMessages::MISSING_MANDATORY_FIELD);
                }
                break;
        }
    }

    /**
     * Encrypt password
     * @return string crypt encrypted password.
     */
    public function encryptPassword($password = '')
    {
        $pass = ('' == $password ? $this->password : $password);
        return crypt($pass);
    }

    /**
     * Generate password to be compared
     * @param string $userInputPassword User input password
     * @param string $dbPassword Password stored in the db
     * @return string generated password to be compared
     */
    public static function getComparingPassword($userInputPassword, $dbPassword)
    {
        return crypt($userInputPassword, $dbPassword);
    }

    /**
     * Check password strength
     * @param string $attribute attribute name
     * @params array $params extra prameters to be passed to validation function
     * @return null
     */
    public function checkPasswordStrength($attribute, $params)
    {
        if ($params['allowEmpty'] && '' == $this->$attribute) {
            return true;
        } else {
            if (preg_match("/^.*(?=.{" . $params['min'] . ",})(?=.*\d)(?=.*[a-zA-Z])(?=.*[-@_#&.]).*$/", $this->$attribute)) {
                return true;
            } else {
                $this->addError($attribute, Yii::t('app', '{attribute} is weak. {attribute} must contain at least {min} characters, at least one letter, at least one number and at least one symbol(-@_#&.).', ['min' => $params['min'], 'attribute' => $this->getAttributeLabel($attribute)]));
            }
        }
    }

    /**
     * Generate new password for reset, according to password plicy
     * @return string $password random password. e.g.: abcd123.@
     */
    public function getNewPassword()
    {
        $letters = 'bcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ';
        $numbers = '0123456789';
        $symbols = '-@_#&.';

        $password = substr(str_shuffle($letters), 0, 4);
        $password .= substr(str_shuffle($numbers), 0, 2);
        $password .= substr(str_shuffle($symbols), 0, 2);

        return $password;
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
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Get any of user attribute by its id
     * @parm string $attribute attribute name
     */
    public function getAttributeValueById($id, $attribute)
    {
        $model = User::findOne($id);
        if (!empty($model))
            return $model->$attribute;
        else
            return '';
    }


    public static function getFullNameById($id)
    {
        if ($id === null)
            return null;

        $model = self::find($id)->one();

        if ($model === null)
            return null;

        return $model->firstName . ' ' . $model->lastName;
    }

    /**
     * Returns statuses list for dropdown.
     * @return array statuses
     */
    public function getStatusesList($prompt = false)
    {
        return $prompt ? (array('' => Yii::t('app', '- Status -')) + $this->_statuses) : $this->_statuses;
    }

	/**
     * Return users list for dropdown options
     */
	public static function getUserList()
	{
		$list = [];
        $models = User::find()->where([])->all();
        if (!empty($models)) {
            foreach ($models as $model) {
                $list[$model->id] = "{$model->firstName} {$model->lastName}";
            }
        }

        return $list;
	}

    /**
     * Return user type options for drop down
     */
    public function getTypeList()
    {
        return [
            self::TENANT => Yii::t('app', 'Tenant'),
            self::OWNER => Yii::t('app', 'Owner')
        ];
    }

    /**
     * Return user type options for drop down
     */
    public function getCompanyTypeList()
    {
        return [
            self::CT_PERSONAL => Yii::t('app', 'Personal'),
            self::CT_PROPERTY_MANAGEMENT => Yii::t('app', 'Property Management'),
            self::CT_BUILDING_MANAGEMENT => Yii::t('app', 'Building Management'),
            self::CT_REAL_STATE_AGENCY => Yii::t('app', 'Real Estate Agency'),
        ];
    }

    /**
     * @return Full name of the user
     */
    public function getFullName()
    {
        return "{$this->firstName} {$this->lastName}";
    }

    /**
     * Authenticate user, depending on different login types
     * @return mixed
     */
    public function authUser()
    {
        $model = false;
        switch ($this->loginType) {
            case self::LT_EMAIL:
                $model = $this->authEmail();
                break;
            case self::LT_FACEBOOK:
                $model = $this->authFacebook();
                break;
            case self::LT_LINKEDIN:
                $model = $this->authLinkedIn();
                break;
            case self::LT_GOOGLE_PLUS:
                $model = $this->authGooglePlus();
                break;
        }

        return $model;
    }

    /**
     * Email authentication
     * @return mixed
     */
    public function authEmail()
    {
        $model = User::find()->where('email = :email', [':email' => $this->email])->one();
        if (!empty($model)) {
            $userPasswordHash = User::getComparingPassword(base64_decode($this->password), $model->password);
            if ($model->password === $userPasswordHash) {
                return $model;
            }
        }

        return false;
    }

    /**
     * Facebook authentication
     * @return mixed
     */
    public function authFacebook()
    {
        $model = User::find()->where('fbId = :fbId', [':fbId' => $this->fbId])->one();
        if (!empty($model)) {
            return $model;
        }
        return false;
    }

    /**
     * LinkedIn authentication
     * @return mixed
     */
    public function authLinkedIn()
    {
        $model = User::find()->where('linkedInId = :linkedInId', [':linkedInId' => $this->linkedInId])->one();
        if (!empty($model)) {
            return $model;
        }
        return false;
    }

    /**
     * Google+ authentication
     * @return mixed
     */
    public function authGooglePlus()
    {
        $model = User::find()->where('gplusId = :gplusId', [':gplusId' => $this->gplusId])->one();
        if (!empty($model)) {
            return $model;
        }
        return false;
    }

    /**
     * Calculate registration counts for each day
     * @param integer $days Number of back days from now
     * @return array
     */
    public function getRegCountsByDate($days = 7)
    {
        $data = [];
        $data[] = [Yii::t('app', 'Date'), Yii::t('app', 'Count')];
        for ($i=0; $i<$days; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $count = User::find()->where('DATE(createdAt) = :createdAt', [':createdAt' => $date])->count();
            $data[] = [$date, (int)$count];
        }

        return $data;
    }

    /**
     * In user creation check whether user has provided at least one login type
     * Either email/password, Facebook, LinkedIn or G+
     * @return boolean
     */
    public function isAnySignupParamExists()
    {
        $emailLogin = false;
        if (null != $this->email && null != $this->password) {
            $emailLogin = true;
        }

        if (!$emailLogin && null == $this->fbId && null == $this->gplusId && null == $this->linkedInId) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve list of property owners where tenant used
     * @param integer $tenantUserId User id of the tenant
     * @return mixed
     */
    public function getMyOwners($tenantUserId)
    {
        $ownerList = [];
        $ownerUserIds = [];

        // Retrieve past owners
        $propHistories = PropertyHistory::find()
            ->andWhere(['tenantUserId' => $tenantUserId])
            ->joinWith('ownerUser')
            ->all();

        if (!empty($propHistories)) {
            foreach ($propHistories as $propHistory) {
                $ownerList[] = $propHistory->ownerUser;
                $ownerUserIds[] = $propHistory->ownerUser->id;
            }
        }

        // Retrieve current owner
        /*$properties = Property::find()
            ->andWhere(['tenantUserId' => $tenantUserId])
            ->joinWith('ownerUser')
            ->all();

        if (!empty($properties)) {
            foreach ($properties as $property) {
                if (!in_array($property->ownerUser->id, $ownerUserIds)) {
                    $ownerList[] = $property->ownerUser;
                }
            }
        }*/

        // TODO: This is just for testing purpose. Please remove in production
        /*$owners = User::find()
            ->andWhere('type=1')
            ->all();*/

        if (!empty($owners)) {
            $ownerList = $owners;
        }
        /// End

        return $ownerList;
    }

    /**
     * Update average user rating when new he received a new review
     * @param integer $userId User id
     * @return mixed
     */
    public function updateRating($userId)
    {
        $userReview = new UserReview();
        $model = User::findOne($userId);
        $avgRating = $userReview->getAvgUserRating($userId);

        $model->rating = $avgRating;

        return $model->saveModel();
    }

    /**
     * User profile picture
     * @param boolean $isApi whether request is from API or Admin web
     * @return string picture URL
     */
    public function getProfileImg($isApi = false)
    {
        $profImgUrl = '';

        if (!empty($this->profileImage)) {
            if (stristr($this->profileImage, 'http://') || stristr($this->profileImage, 'https://')) {
                $profImgUrl = $this->profileImage;
            } else {
                $aws = new Aws();
                $profImgUrl = $aws->s3GetObjectUrl($this->profileImage, false);
            }
        } else {
            if (!$isApi) {
                $profImgUrl = Yii::$app->view->theme->baseUrl . '/img/blank_user.png';
            }
        }

        return $profImgUrl;
    }

    /**
     * User profile thumbnail picture
     * @param boolean $isApi whether request is from API or Admin web
     * @return string picture URL
     */
    public function getProfileImgThumbnail($isApi = false)
    {
        $profImgUrl = '';

        if (!empty($this->profileImageThumb)) {
            $aws = new Aws();
            $profImgUrl = $aws->s3GetObjectUrl($this->profileImageThumb, false);
        } else {
            if (!$isApi) {
                if (stristr($this->profileImage, 'http://') || stristr($this->profileImage, 'https://')) {
                    // For social logins there is no thumbnail image only profile image
                    $profImgUrl = $this->profileImage;
                } else {
                    $profImgUrl = Yii::$app->view->theme->baseUrl . '/img/blank_user.png';
                }
            }
        }

        return $profImgUrl;
    }

    /**
     * Retrieve user profile by email
     * @param string $email
     * @return mixed
     */
    public function getUserByEmail($email)
    {
        $user = User::find()
            ->andWhere(['email' => $email])
            ->one();

        return $user;
    }

    /**
     * Get user by phone
     * @param string $phone
     * @return mixed
     */
    public function getUserByPhone($phone)
    {
        $user = User::find()
            ->andWhere(['phone' => $phone])
            ->one();

        return $user;
    }

    /**
     * Retrieve user profile by password reset token
     * @param string $token
     * @return mixed
     */
    public function getUserByPwResetToken($token)
    {
        $user = User::find()
            ->andWhere(['passwordResetToken' => $token])
            ->one();

        return $user;
    }

    /**
     * Upload LinkedIn profile picture to our S3 bucket. If user change his profile picture, previous URL is not
     * available (404)
     * @return mixed
     */
    public function getProfPic()
    {
        $image = new Image();
        $aws = new Aws();
        $picInfo = ['profileImage' => $this->profileImage, 'profileImageThumb' => $this->profileImageThumb];
        if ($this->profileImageThumb == '' && $this->linkedInId != '') {
            $content = file_get_contents($this->profileImage);
            $fileName = str_replace(['{timestamp}', '{random}'], [time(), rand(0,999)], self::PROF_PIC_NAME);
            $filePath = Yii::$app->params['tempPath'] . $fileName;
            if (file_put_contents($filePath, $content)) {
                $ext = $image->getImageExt($filePath);
                $fileName .= ".{$ext}";

                $aws->s3UploadObject($fileName, $filePath, ['ACL' => 'public-read']);
                $fileUrl = $aws->s3GetObjectUrl($fileName, false);

                $picInfo = ['profileImage' => $fileName, 'profileImageThumb' => $fileName];
            }
        }

        return $picInfo;
    }

    /**
     * Retrieve user by id
     * @return mixed
     */
    public function getUserById($id)
    {
        return User::findOne($id);
    }

    /**
     * Get users
     * @return mixed
     */
    public function getUsers($page)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = User::find();
        $query->limit($limit);
        $query->offset($offset);

        $users = $query->all();

        return $users;
    }
}
