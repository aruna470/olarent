<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserReview;
use app\modules\api\components\ApiStatusMessages;

/**
 * UserReviewSearch represents the model behind the search form about `app\models\UserReview`.
 */
class UserReviewSearch extends UserReview
{
    const SCENARIO_API_SEARCH = 'apiSearch';

    public $limit = 10;
    public $page = 1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // API Search
            [['limit', 'page'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => self::SCENARIO_API_SEARCH],
            [['limit', 'page'], 'integer', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => self::SCENARIO_API_SEARCH],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // Bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserReview::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Uncomment the following line if you do not want to return any records when validation fails
            //$query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'userId' => $this->userId,
            'reviewedUserId' => $this->reviewedUserId,
            'rating' => $this->rating,
            'createdAt' => $this->createdAt,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }

    /**
     * Search for API requests
     * @return mixed
     */
    public function apiSearch()
    {
        $offset = ($this->page - 1) * $this->limit;

        $query = UserReview::find();
        $query->andFilterWhere(['userId' => $this->userId]);
        $query->joinWith(['user', 'reviewedUser']);
        $query->limit($this->limit);
        $query->offset($offset);
        $query->orderBy([UserReview::tableName() . '.createdAt' => SORT_DESC]);

        $total = $query->count();
        $userReviews = $query->all();

        return ['total' => $total, 'userReviews' => $userReviews] ;
    }
}
