<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Property;
use app\modules\api\components\ApiStatusMessages;

/**
 * PropertySearch represents the model behind the search form about `app\models\Property`.
 */
class PropertySearch extends Property
{
    const SCENARIO_API_SEARCH = 'apiSearch';

    public $limit = 10;
    public $page = 1;

    // For searching
    public $smartSearchParams;
    public $ownerName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'ownerUserId', 'tenantUserId', 'name', 'description', 'address', 'geoLocation', 'imageName', 'currentRentDueAt',
                'createdAt', 'updatedAt', 'reservedAt', 'nextChargingDate', 'status'], 'safe'],

            // API Search
            [['limit', 'page'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => self::SCENARIO_API_SEARCH],
            [['limit', 'page'], 'integer', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => self::SCENARIO_API_SEARCH],
            [['ownerUserId', 'tenantUserId'], 'integer', 'message' => ApiStatusMessages::VALIDATION_FAILED, 'on' => self::SCENARIO_API_SEARCH],
            [['code', 'smartSearchParams', 'ownerName', 'status'], 'safe', 'on' => self::SCENARIO_API_SEARCH]
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
        $query = Property::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['createdAt' => SORT_DESC]]
        ]);

        $this->load($params);

        $this->trimAttributes();

        if (!$this->validate()) {
            // Uncomment the following line if you do not want to return any records when validation fails
            //$query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['Property.status' => $this->status])
            ->andFilterWhere(['ou.id' => $this->ownerUserId])
            ->andFilterWhere(['tu.id' => $this->tenantUserId]);

        $query->joinWith(['ownerUser', 'tenantUser']);

        return $dataProvider;
    }

    /**
     * Search for API requests
     * @return mixed
     */
    public function apiSearch()
    {
        $offset = ($this->page - 1) * $this->limit;

        $query = Property::find();
        $query->andFilterWhere(['code' => $this->code])
            ->andFilterWhere([self::tableName() . '.status' => $this->status])
            ->andFilterWhere(['like', 'CONCAT(ou.firstName, \' \', ou.lastName)', $this->ownerName])
            ->andFilterWhere(['ou.id' => $this->ownerUserId])
            ->andFilterWhere(['tu.id' => $this->tenantUserId]);
//            ->andFilterWhere(['like', 'CONCAT(ou.firstName, \' \', ou.lastName)', $this->ownerNameOrCode])
//            ->orFilterWhere(['code' => $this->ownerNameOrCode]);

        $query->joinWith(['ownerUser', 'tenantUser']);
        $query->limit($this->limit);
        $query->offset($offset);

        $total = $query->count();
        $properties = $query->all();

        return ['total' => $total, 'properties' => $properties] ;
    }

    /**
     * Perform search on city,firstName,lastName or code
     * @return mixed
     */
    public function apiSmartSearch()
    {
        $offset = ($this->page - 1) * $this->limit;

        $propTableName = self::tableName();
        $query = Property::find();
        $query->where("{$propTableName}.status = :status
            AND {$propTableName}.isOnBhf != :isOnBhf
            AND (CONCAT(ou.firstName, ' ', ou.lastName) LIKE '%{$this->smartSearchParams}%'
            OR city LIKE '%{$this->smartSearchParams}%'
            OR code LIKE '%{$this->smartSearchParams}%')",
            [':status' => $this->status, ':isOnBhf' => Property::ON_BEHALF_YES]
        );

        $query->joinWith(['ownerUser', 'tenantUser']);
        $query->limit($this->limit);
        $query->offset($offset);

        $total = $query->count();
        $properties = $query->all();

        return ['total' => $total, 'properties' => $properties] ;
    }

    /**
     * Retrieve payment due properties
     * @return mixed
     */
   /* public function getPaymentDueProperties()
    {
        $query = Property::find();
        $query->andWhere(['paymentStatus' => self::PS_FAILED])
            ->andWhere([self::tableName() . '.status' => self::STATUS_NOT_AVAILABLE])
            ->andWhere(['tu.id' => $this->tenantUserId])
            ->andWhere(['reachMaxAttempts' => self::REACH_MAX_ATT_YES]);

        $query->joinWith(['ownerUser', 'tenantUser']);

        $total = $query->count();
        $properties = $query->all();

        return ['total' => $total, 'properties' => $properties] ;
    }*/

    /**
     * Retrieve payment due properties
     * @return mixed
     */
    public function getPaymentDueProperties()
    {
        $query = Property::find();
        $query->andWhere([self::tableName() . '.status' => self::STATUS_NOT_AVAILABLE])
            ->andWhere(['tu.id' => $this->tenantUserId])
            ->andFilterWhere([self::tableName() . '.isOnBhf' => $this->isOnBhf]);

        $query->joinWith(['ownerUser', 'tenantUser']);

        $total = $query->count();
        $properties = $query->all();

        return ['total' => $total, 'properties' => $properties] ;
    }
}
