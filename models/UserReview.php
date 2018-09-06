<?php

namespace app\models;

use Yii;
use app\models\Base;
use app\models\ReviewRequest;
use app\modules\api\components\ApiStatusMessages;

/**
 * This is the model class for table "UserReview".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $reviewedUserId
 * @property integer $rating
 * @property string $title
 * @property string $comment
 * @property string $createdAt
 *
 * @property User $user
 * @property User $reviewedUser
 */
class UserReview extends Base
{
    // Validation scenarios
    const SCENARIO_API_CREATE = 'apiCreate';

    public $reviewRequestId;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'UserReview';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // API create
            [['userId', 'reviewedUserId', 'title', 'rating', 'createdAt', 'reviewRequestId'], 'required',
                'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => self::SCENARIO_API_CREATE],
            [['userId', 'reviewedUserId', 'rating'], 'integer', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => self::SCENARIO_API_CREATE],
            [['rating'], 'number', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => self::SCENARIO_API_CREATE],// Custom message not work with min max attributes.
            [['rating'], 'validateRating', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => self::SCENARIO_API_CREATE],
            [['title'], 'string', 'max' => 45, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => self::SCENARIO_API_CREATE],
            [['comment'], 'string', 'max' => 145, 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => self::SCENARIO_API_CREATE],
            [['reviewRequestId'], 'isDuplicateReview', 'message' => ApiStatusMessages::VALIDATION_FAILED,
                'on' => self::SCENARIO_API_CREATE]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'reviewedUserId' => Yii::t('app', 'Reviewed User ID'),
            'rating' => Yii::t('app', 'Rating'),
            'title' => Yii::t('app', 'Title'),
            'comment' => Yii::t('app', 'Comment'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    public function isDuplicateReview()
    {
        if (!$this->isReviewed($this->reviewRequestId)) {
            $this->addError('reviewRequestId', ApiStatusMessages::DUPLICATE_REVIEW);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId'])
            ->from(User::tableName() . ' u');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReviewedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'reviewedUserId'])
            ->from(User::tableName() . ' revU');
    }

    /**
     * Check whether user has already reviewed
     * @param integer $revReqId
     * @return boolean
     */
    public function isReviewed($revReqId)
    {
        $reviewRequest = ReviewRequest::findOne($revReqId);
        if (!empty($reviewRequest) && $reviewRequest->status == ReviewRequest::STATUS_PENDING) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Calculate average user rating
     * @param integer $userId User id
     * @return mixed
     */
    public function getAvgUserRating($userId)
    {
        $avgRating = 0;

        $totReviews = UserReview::find()
            ->andWhere(['userId' => $userId])
            ->count();

        $totRating = UserReview::find()
            ->andWhere(['userId' => $userId])
            ->sum('rating');

        if ($totReviews > 0) {
            $avg = $totRating/$totReviews;
            $avgRating = number_format($avg, 2, '.', '');
        }

        return $avgRating;
    }

    public function validateRating()
    {
        if ($this->rating <= 0 || $this->rating > 5) {
            $this->addError('rating', ApiStatusMessages::VALIDATION_FAILED);
        }
    }
}
