<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Payment;

/**
 * PaymentSearch represents the model behind the search form about `app\models\Payment`.
 */
class PaymentSearch extends Payment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'payerUserId', 'payeeUserId', 'propertyId', 'type'], 'integer'],
            [['amount'], 'number'],
            [['adyenPspReference', 'adyenTransactionReference', 'createdAt', 'paymentForDate', 'propertyName',
            'propertyCode'], 'safe'],
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
        $query = Payment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['createdAt' => SORT_DESC]]
        ]);

        $this->load($params);
        $this->propertyName = trim($this->propertyName);
        $this->propertyCode = trim($this->propertyCode);
       // $this->trimAttributes();

        $query->andFilterWhere([
            'payerUserId' => $this->payerUserId,
            'payeeUserId' => $this->payeeUserId
        ]);

        $query->andFilterWhere(['like', Property::tableName() . '.name', $this->propertyName]);
        $query->andFilterWhere([Property::tableName() . '.code' => $this->propertyCode]);

        $query->joinWith(['payerUser', 'payeeUser', 'property']);

        return $dataProvider;
    }
}
