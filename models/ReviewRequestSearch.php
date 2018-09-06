<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ReviewRequest;
use app\modules\api\components\ApiStatusMessages;

/**
 * ReviewRequestSearch represents the model behind the search form about `app\models\ReviewRequest`.
 */
class ReviewRequestSearch extends ReviewRequest
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
        $query = ReviewRequest::find();

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
            'requesterUserId' => $this->requesterUserId,
            'receiverUserId' => $this->receiverUserId,
            'createdAt' => $this->createdAt,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }


    /**
     * Search for API requests
     * @return mixed
     */
    public function apiSearch()
    {
        $offset = ($this->page - 1) * $this->limit;

        $query = ReviewRequest::find();
        $query->andFilterWhere(['receiverUserId' => $this->receiverUserId]);
        $query->andFilterWhere([self::tableName() . '.status' => $this->status]);
        $query->joinWith(['requesterUser', 'receiverUser']);
        $query->limit($this->limit);
        $query->offset($offset);
        $query->orderBy([ReviewRequest::tableName() . '.createdAt' => SORT_DESC]);

        $total = $query->count();
        $reviewRequests = $query->all();

        return ['total' => $total, 'reviewRequests' => $reviewRequests] ;
    }
}