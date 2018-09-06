<?php

namespace app\models;

use Yii;
use app\models\Base;
use app\modules\api\components\Messages;
use app\components\Aws;

/**
 * This is the model class for table "File".
 *
 * @property integer $id
 * @property string $fileName
 * @property string $comment
 * @property integer $type
 * @property integer $userId
 *
 * @property User $user
 */
class File extends Base
{
    const TYPE_TAX = 1; // Tax files
    const TYPE_IP = 2;  // Income proof
    const TYPE_BG = 3;  // Bank gurantee
    const TYPE_CS = 4;  // Co-Signer
    const TYPE_COS = 5; // Caution solidare
    const TYPE_ID = 6;  // User Identity

    public $fileTypes;
    public $fileTypeStr;

    public function init()
    {
        parent::init();

        $this->fileTypes = [
            self::TYPE_TAX => Yii::t('app', 'Tax File'),
            self::TYPE_IP => Yii::t('app', 'Income Proof'),
            self::TYPE_BG => Yii::t('app', 'Bank Guarantee'),
            self::TYPE_CS => Yii::t('app', 'Co-Signer'),
            self::TYPE_COS => Yii::t('app', 'Caution Solidare'),
            self::TYPE_ID => Yii::t('app', 'Identity'),
        ];

        foreach ($this->fileTypes as $key => $value) {
            $this->fileTypeStr .= "{$key}|";
        }

        $this->fileTypeStr = rtrim($this->fileTypeStr, "|");
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'File';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fileName', 'type', 'userId'], 'required'],
            [['type', 'userId'], 'integer'],
            [['fileName'], 'string', 'max' => 30],
            [['comment'], 'string', 'max' => 64],
            [['type'], 'match', 'pattern' => "/{$this->fileTypeStr}/"],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fileName' => Yii::t('app', 'File Name'),
            'comment' => Yii::t('app', 'Comment'),
            'type' => Yii::t('app', 'Type'),
            'userId' => Yii::t('app', 'User ID'),
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
     * Retrieve files associated with particular user
     * @param integer $userId Id of the user
     * @return array
     */
    public static function getFileListByUserId($userId)
    {
        $aws = new Aws();
        $files = File::find()->where('userId = :userId', [':userId' => $userId])
            ->orderBy(['type' => SORT_DESC])
            ->all();
        $fileList = [];
        if (!empty($files)) {
            foreach ($files as $file) {
                //$fileUrl = Yii::$app->params['contentBaseUrl'] . $file->fileName;
                $fileUrl = $aws->s3GetObjectUrl($file->fileName);
                $fileList[] = Messages::file($file, $fileUrl);
            }
        }

        return $fileList;
    }

    /**
     * Add set of files
     * @param array $userFiles List of files to be added
     * @param integer $userId
     * @return boolean
     */
    public static function addFiles($userFiles, $userId)
    {
        $isAllSuccess = true;
        if (!empty($userFiles)) {
            foreach ($userFiles as $userFile) {
                $file = new File();
                $file->attributes = $userFile;
                $file->userId = $userId;
                if (!$file->saveModel()) {
                    $isAllSuccess = false;
                    break;
                }
            }
        }

        return $isAllSuccess;
    }

    /**
     * Get file list with S3 URLs
     * @param integer $userId Id of the user
     * @return array
     */
    public function getFileList($userId)
    {
        $aws = new Aws();
        $files = File::find()->where('userId = :userId', [':userId' => $userId])->all();
        $fileList = [];
        if (!empty($files)) {
            foreach ($files as $file) {
                $fileUrl = $aws->s3GetObjectUrl($file->fileName);
                $fileList[] = [
                    'fileName' => $file->fileName,
                    'comment' => $file->comment,
                    'type' => $this->fileTypes[$file->type],
                    'fileUrl' => $fileUrl
                ];
            }
        }
        return $fileList;
    }
}
