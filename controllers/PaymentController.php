<?php

namespace app\controllers;

use Yii;
use app\models\Payment;
use app\models\User;
use app\models\PaymentSearch;
use app\controllers\BaseController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends BaseController
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
     * Lists all Payment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $urlOwner = Url::to(['user/get-users-by-type', 'type' => User::OWNER]);
        $urlTenant = Url::to(['user/get-users-by-type', 'type' => User::TENANT]);

        $owner = User::findOne($searchModel->payeeUserId);
        $tenant = User::findOne($searchModel->payerUserId);

        $ownerName = !empty($owner) ? "{$owner->firstName} {$owner->lastName}" : '';
        $tenantName = !empty($tenant) ? "{$tenant->firstName} {$tenant->lastName}" : '';

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'urlTenant' => $urlTenant,
            'urlOwner' => $urlOwner,
            'ownerName' => $ownerName,
            'tenantName' => $tenantName,
        ]);
    }

    /**
     * Displays a single Payment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Payment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Payment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
