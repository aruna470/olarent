<?php

namespace app\controllers;

use Yii;
use app\models\Property;
use app\models\User;
use app\models\PropertySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;

/**
 * PropertyController implements the CRUD actions for Property model.
 */
class PropertyController extends BaseController
{
    public function behaviors()
    {
        return [

        ];
    }

    public function allowed()
    {
        return ['Property.GetUsersByType'];
    }

    /**
     * Lists all Property models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PropertySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $urlOwner = Url::to(['user/get-users-by-type', 'type' => User::OWNER]);
        $urlTenant = Url::to(['user/get-users-by-type', 'type' => User::TENANT]);

        $owner = User::findOne($searchModel->ownerUserId);
        $tenant = User::findOne($searchModel->tenantUserId);

        $ownerName = !empty($owner) ? "{$owner->firstName} {$owner->lastName}" : '';
        $tenantName = !empty($tenant) ? "{$tenant->firstName} {$tenant->lastName}" : '';

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'urlTenant' => $urlTenant,
            'urlOwner' => $urlOwner,
            'ownerName' => $ownerName,
            'tenantName' => $tenantName,
            'statuses' => $searchModel->getStatusList()
        ]);
    }

    /**
     * Displays a single Property model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $this->layout = 'popup';
        $model = Property::find()->with('ownerUser', 'tenantUser')->where(['id' => $id])->one();

        return $this->render('view', [
            'model' => $model,
            'statuses' => $model->getStatusList(),
            'imageList' => $model->getImageList()
        ]);
    }

    /**
     * Finds the Property model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Property the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Property::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
