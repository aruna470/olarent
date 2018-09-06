<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Notification;
use app\modules\api\components\ApiStatusMessages;

/**
 * NotificationSearch represents the model behind the search form about `app\models\Notification`.
 */
class NotificationSearch extends Notification
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
            [['userId', 'limit', 'page'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD, 'on' => self::SCENARIO_API_SEARCH],
            [['viewStatus'], 'safe']
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
        $query = Notification::find();

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
            'viewStatus' => $this->viewStatus,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }

    /**
     * Search for API requests
     * @return mixed
     */
    public function apiSearch()
    {
        $offset = ($this->page - 1) * $this->limit;

        $query = Notification::find();
        $query->andFilterWhere(['userId' => $this->userId])
            ->andFilterWhere(['viewStatus' => $this->viewStatus]);
        $query->limit($this->limit);
        $query->offset($offset);
        $query->orderBy(['createdAt' => SORT_DESC]);

        $total = $query->count();
        $notifications = $query->all();

        return ['total' => $total, 'notifications' => $notifications] ;
    }
}
