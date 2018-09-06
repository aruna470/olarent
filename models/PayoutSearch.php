<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Payout;

/**
 * PayoutSearch represents the model behind the search form about `app\models\Payout`.
 */
class PayoutSearch extends Payout
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'paymentId', 'userId', 'mpPayoutExecutionDate', 'retryCount'], 'integer'],
            [['mpTransferId', 'mpTransferStatus', 'mpPayoutId', 'mpPayoutStatus', 'createdAt', 'mpBankAccountId',
                'mpTransferMessage', 'mpPayoutMessage', 'propertyCode'], 'safe'],
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
        $query = Payout::find();

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

        $query->andFilterWhere(['userId' => $this->userId])
            ->andFilterWhere([Property::tableName() . '.code' => $this->propertyCode])
            ->joinWith(['user', 'payment', 'payment.property']);

        return $dataProvider;
    }
}
