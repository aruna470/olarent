<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Permission;

/**
 * PermissionSearch represents the model behind the search form about `app\models\Permission`.
 */
class PermissionSearch extends Permission
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'category', 'createdAt', 'updatedAt'], 'safe'],
            [['createdById', 'updatedById'], 'integer'],
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
        $permissions = Permission::find()
            ->orderBy('category')
            ->all();

        $models = [];

        foreach ($permissions as $permission) {
            if (Yii::$app->user->can($permission->name)) {
                $models[] = $permission;
            }
        }

        $dataProvider = new ActiveDataProvider([
            'models' => $models,
            'pagination' => false
        ]);

        return $dataProvider;
    }
}
