<?php

namespace app\models;

use Yii;
use app\models\Base;
use app\modules\api\components\ApiStatusMessages;
use app\components\Aws;
use app\components\Mp;
use app\components\Mail;

/**
 * This is the model class for table "UserMpInfoFile".
 *
 * @property integer $id
 * @property integer $userMpInfoId
 * @property integer $userId
 * @property string $fileName
 * @property integer $type
 * @property integer $status
 * @property string $mpDocId
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property User $user
 * @property Usermpinfo $userMpInfo
 */
class UserMpInfoFile extends Base
{
    // File types
    const FT_IDENTITY_PROOF = 1;

    // Validation scenarios
    const SCENARIO_API_CREATE = 'apiCreate';

    // File statuses
    const FS_VALIDATION_ASKED = 1;
    const FS_SUCCESS = 2;
    const FS_FAILED = 3;

    public $fileTypes = [];
    public $fileStatuses = [];

    public function init()
    {
        $this->fileTypes = [
            self::FT_IDENTITY_PROOF => Yii::t('app', 'Identity Proof')
        ];

        $this->fileStatuses = [
            self::FS_VALIDATION_ASKED => Yii::t('app', 'Pending'),
            self::FS_SUCCESS => Yii::t('app', 'Success'),
            self::FS_FAILED => Yii::t('app', 'Failed'),
        ];

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'UserMpInfoFile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Common
            [['userMpInfoId', 'userId', 'type', 'status'], 'integer',
                'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => [self::SCENARIO_API_CREATE]],
            [['createdAt', 'updatedAt'], 'safe', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['mpDocId'], 'string', 'max' => 10, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['fileName'], 'string', 'max' => 30, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => [self::SCENARIO_API_CREATE]],
            [['fileName'], 'file', 'extensions' => 'jpg, png, pdf, jpeg', 'on' => [self::SCENARIO_API_CREATE]],
            [['type'], 'in', 'range' => [self::FT_IDENTITY_PROOF], 'on' => [self::SCENARIO_API_CREATE]],

            // API Create
            [['userMpInfoId', 'userId', 'fileName', 'type', 'createdAt'], 'required',
                'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => [self::SCENARIO_API_CREATE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userMpInfoId' => Yii::t('app', 'User Mp Info ID'),
            'userId' => Yii::t('app', 'User ID'),
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
            'mpDocId' => Yii::t('app', 'MP Doc ID'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getUserMpInfo()
    {
        return $this->hasOne(Usermpinfo::className(), ['id' => 'userMpInfoId']);
    }

    /**
     * Upload file to MangoPay and ask for validity
     * @param UserMpInfo $userMpInfo
     * @return boolean
     */
    public function createFile($userMpInfo)
    {
        $status = false;
        $aws = new Aws();
        $mp = new Mp(Yii::$app->params['mangoPay']);

        $type = $this->type == self::FT_IDENTITY_PROOF ? Mp::IDENTITY_PROOF : '';

        $s3Url = $aws->s3GetObjectUrl($this->fileName, true);
        $fileContentRow = file_get_contents($s3Url);

        if (!empty($fileContentRow)) {
            $resDoc = $mp->createKycDocument($userMpInfo->mpUserId, $type);
            if (isset($resDoc->Id)) {
                $resUpload = $mp->uploadKycDocument($userMpInfo->mpUserId, $resDoc->Id, base64_encode($fileContentRow));
                if ($resUpload) {
                    $resUpdate = $mp->updateKycDocument($userMpInfo->mpUserId, $resDoc->Id);
                    if (isset($resUpdate->Id)) {
                        $this->mpDocId = $resDoc->Id;
                        $this->status = self::FS_VALIDATION_ASKED;
                        if ($this->saveModel()) {
                            $status = true;
                        }
                    }
                }
            }
        } else {
            $this->log->writeLog('File content retrieval failed.');
        }


        return $status;
    }

    /**
     * Retrieve all files related to user
     * @return boolean
     */
    public function getFiles()
    {
        return self::find()->where(['userId' => $this->userId])->all();
    }

    /**
     * Retrieve file URL
     * @param string $fileName Name of the File
     * @return string S3 file URL
     */
    public function getFileUrl($fileName = null)
    {
        $fName = null == $fileName ? $this->fileName : $fileName;
        $fileUrl = '';
        $aws = new Aws();
        if (null != $fName) {
            $fileUrl = $aws->s3GetObjectUrl($fName, true);
        }
        return $fileUrl;
    }

    /**
     * Update KYC document status
     * @param string $mpDocId MangoPay document resource id
     * @param string $eventType MangoPay KYC event type
     * @return UserMpInfoFile
     */
    public function updateDocumentStatus($mpDocId, $eventType)
    {
        $model = self::find()
            ->where(['mpDocId' => $mpDocId])
            ->joinWith('user')
            ->one();

        $mail = new Mail();

        if (!empty($model)) {
            switch ($eventType) {
                case 'KYC_SUCCEEDED':
                    $model->status = self::FS_SUCCESS;
                    break;

                case 'KYC_FAILED':
                    $model->status = self::FS_FAILED;
                    $mail->documentValidateFail($model->user->email, $model->user->firstName);
                    break;
            }
            $model->saveModel();
        } else {
            $this->log->writeLog('Document not found.');
        }
    }
}
