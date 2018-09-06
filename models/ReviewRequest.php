<?php

namespace app\models;

use Yii;
use app\models\Base;
use app\modules\api\components\ApiStatusMessages;

/**
 * This is the model class for table "ReviewRequest".
 *
 * @property integer $id
 * @property integer $requesterUserId
 * @property integer $receiverUserId
 * @property string $createdAt
 * @property integer $status
 *
 * @property User $requesterUser
 * @property User $receiverUser
 */
class ReviewRequest extends Base
{
    // Validation scenarios
    const SCENARIO_API_CREATE = 'apiCreate';

    // Statuses
    const STATUS_PENDING = 0;
    const STATUS_REVIEWED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ReviewRequest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['requesterUserId', 'receiverUserId', 'createdAt', 'status'], 'required', 'on' => self::SCENARIO_API_CREATE,
                'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD],
            [['requesterUserId', 'receiverUserId', 'status'], 'integer', 'on' => self::SCENARIO_API_CREATE,
                'message' => ApiStatusMessages::VALIDATION_FAILED],
            [['requesterUserId'], 'isExists', 'message' => ApiStatusMessages::RECORD_EXISTS,
                'on' => [self::SCENARIO_API_CREATE]],
            [['createdAt'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'requesterUserId' => Yii::t('app', 'Requester User ID'),
            'receiverUserId' => Yii::t('app', 'Receiver User ID'),
            'createdAt' => Yii::t('app', 'Created At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequesterUser()
    {
        return $this->hasOne(User::className(), ['id' => 'requesterUserId'])
            ->from(User::tableName() . ' reqU');

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiverUser()
    {
        return $this->hasOne(User::className(), ['id' => 'receiverUserId'])
            ->from(User::tableName() . ' revU');
    }

    /**
     * Validate request existence
     */
    public function isExists()
    {
        if ($this->isAlreadyRequested($this->requesterUserId, $this->receiverUserId)) {
            $this->addError('receiverUserId', ApiStatusMessages::RECORD_EXISTS);
        }
    }

    /**
     * Check whether user has already made a property request
     * @param integer $requesterUserId User id of the review requester
     * @param integer $receiverUserId User id of the review request receiver
     * @return boolean
     */
    public function isAlreadyRequested($requesterUserId, $receiverUserId)
    {
        $model = ReviewRequest::find()
            ->andWhere('receiverUserId = :receiverUserId', [':receiverUserId' => $receiverUserId])
            ->andWhere('requesterUserId = :requesterUserId', [':requesterUserId' => $requesterUserId])
            ->one();

        if (!empty($model)) {
            return true;
        }

        return false;
    }

    /**
     * Update review request status
     * @param integer $id Review request id
     * @param integer $status Status
     * @return boolean
     */
    public function updateStatus($id, $status)
    {
        $model = ReviewRequest::findOne($id);
        if (!empty($model)) {
            $model->status = $status;
            return $model->saveModel();
        }

        return false;
    }
}
