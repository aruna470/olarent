<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CompanyWallet;

/**
 * CompanyWalletSearch represents the model behind the search form about `app\models\CompanyWallet`.
 */
class CompanyWalletSearch extends CompanyWallet
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'incomeRange', 'createdById'], 'integer'],
            [['email', 'firstName', 'lastName', 'birthDate', 'nationality', 'countryOfResidence', 'occupation', 'createdAt', 'updatedAt', 'mpUserId', 'mpWalletId'], 'safe'],
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
        $query = CompanyWallet::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Uncomment the following line if you do not want to return any records when validation fails
            //$query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'firstName', $this->firstName])
            ->andFilterWhere(['like', 'lastName', $this->lastName])
            ->andFilterWhere(['like', 'nationality', $this->nationality])
            ->andFilterWhere(['like', 'countryOfResidence', $this->countryOfResidence])
            ->andFilterWhere(['like', 'occupation', $this->occupation])
            ->andFilterWhere(['like', 'mpUserId', $this->mpUserId])
            ->andFilterWhere(['like', 'mpWalletId', $this->mpWalletId]);

        return $dataProvider;
    }
}
