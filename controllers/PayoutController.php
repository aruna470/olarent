<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\Payout;
use app\models\PayoutSearch;
use app\controllers\BaseController;
use app\models\User;


/**
 * PayoutController implements the CRUD actions for Payout model.
 */
class PayoutController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Payout models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PayoutSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $urlOwner = Url::to(['user/get-users-by-type', 'type' => User::OWNER]);
        $owner = User::findOne($searchModel->userId);
        $ownerName = !empty($owner) ? "{$owner->firstName} {$owner->lastName}" : '';

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'urlOwner' => $urlOwner,
            'ownerName' => $ownerName,
        ]);
    }

    /**
     * Displays a single Payout model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $this->layout = 'popup';

        $model = Payout::find()
            ->where([Payout::tableName() . '.id' => $id])
            ->joinWith(['payment', 'user', 'userMpInfo'])
            ->one();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Payout model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Payout the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payout::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
} 