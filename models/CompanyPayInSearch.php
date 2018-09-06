<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CompanyWireIn;

/**
 * CompanyWireInSearch represents the model behind the search form about `app\models\CompanyWireIn`.
 */
class CompanyPayInSearch extends CompanyPayIn
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'createdById'], 'integer'],
            [['wireReference', 'type', 'ownerName', 'ownerAddress', 'bic', 'iban', 'currency', 'status', 'mpWalletId', 'mpUserId', 'createdAt'], 'safe'],
            [['amount'], 'number'],
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
        $query = CompanyPayIn::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['createdAt' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Uncomment the following line if you do not want to return any records when validation fails
            //$query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'wireReference', $this->wireReference])
            ->joinWith(['user']);

        return $dataProvider;
    }
}
