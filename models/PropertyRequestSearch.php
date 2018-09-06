<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PropertyRequest;
use app\modules\api\components\ApiStatusMessages;

/**
 * PropertyRequestSearch represents the model behind the search form about `app\models\PropertyRequest`.
 */
class PropertyRequestSearch extends PropertyRequest
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
            [['id', 'propertyId', 'tenantUserId', 'status', 'payDay', 'bookingDuration', 'code', 'createdAt'], 'safe'],

            // API Search
            [['limit', 'page'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => self::SCENARIO_API_SEARCH],
            [['limit', 'page'], 'integer', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => self::SCENARIO_API_SEARCH],
            [['ownerUserId', 'tenantUserId'], 'integer', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => self::SCENARIO_API_SEARCH],
            [['code'], 'safe', 'on' => self::SCENARIO_API_SEARCH]
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
        $query = PropertyRequest::find();

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
            'propertyId' => $this->propertyId,
            'tenantUserId' => $this->tenantUserId,
            'status' => $this->status,
            'createdAt' => $this->createdAt,
            'payDay' => $this->payDay,
            'bookingDuration' => $this->bookingDuration,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }

    /**
     * Search for API requests
     * @return mixed
     */
    public function apiSearch()
    {
        $offset = ($this->page - 1) * $this->limit;

        $query = PropertyRequest::find();
        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere([self::tableName() . '.status' => $this->status])
            ->andFilterWhere(['propertyId' => $this->propertyId])
            ->andFilterWhere(['ou.id' => $this->ownerUserId])
            ->andFilterWhere(['tu.id' => $this->tenantUserId]);

        $query->joinWith(['ownerUser', 'tenantUser', 'property']);
        $query->limit($this->limit);
        $query->offset($offset);

        $total = $query->count();
        $propertyRequests = $query->all();

        return ['total' => $total, 'propertyRequests' => $propertyRequests] ;
    }
}
