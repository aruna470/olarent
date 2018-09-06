<?php

namespace app\controllers;


use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\CompanyPayIn;
use app\models\CompanyPayInSearch;
use app\controllers\BaseController;
use app\components\Mp;
use app\models\CompanyWallet;


/**
 * CompanyWireInController implements the CRUD actions for CompanyWireIn model.
 */
class CompanyPayInController extends BaseController
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
     * Lists all CompanyWireIn models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CompanyPayInSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CompanyWireIn model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $this->layout = 'popup';

        $model = CompanyPayIn::find()->where(['id' => $id])->with('user')->one();
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new CompanyWireIn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $sucMsg = Yii::t('app', 'Pay In created. You need following details to proceed with wire transfer.');
        $errMsg = Yii::t('app', 'Pay In create failed.');
        $warnMsg = Yii::t('app', 'To create Pay In transaction you need to have a company wallet.');

        $mp = new Mp(Yii::$app->params['mangoPay']);

        $companyWallet = CompanyWallet::find()->where([])->one();

        $model = new CompanyPayIn();
        $model->scenario = CompanyPayIn::SCENARIO_CREATE;

        if (empty($companyWallet)) {
            Yii::$app->session->setFlash('warning', $warnMsg);
            return $this->redirect(['index']);
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $model->currency = Yii::$app->params['defCurrency'];
                if ($model->validateModel(['amount', 'currency'])) {
                    $res = $mp->createPayInBankWire($companyWallet->mpUserId, $companyWallet->mpWalletId, $model->currency, $model->amount);
                    if (isset($res->Id)) {
                        $model->wireReference = $res->PaymentDetails->WireReference;
                        $model->type = $res->Type;
                        $model->ownerName = $res->PaymentDetails->BankAccount->OwnerName;
                        $model->ownerAddress = $res->PaymentDetails->BankAccount->OwnerAddress->AddressLine1;
                        $model->bic = $res->PaymentDetails->BankAccount->Details->BIC;
                        $model->iban = $res->PaymentDetails->BankAccount->Details->IBAN;
                        $model->status = $res->Status;
                        $model->mpWalletId = $companyWallet->mpWalletId;
                        $model->mpUserId = $companyWallet->mpUserId;
                        $model->createdById = Yii::$app->user->identity->id;
                        $model->mpPayInId = $res->Id;
                        if ($model->saveModel()) {
                            Yii::$app->session->setFlash('success', $sucMsg);
                            return $this->redirect(['index']);
                        } else {
                            Yii::$app->session->setFlash('error', $errMsg);
                        }
                    } else {
                        Yii::$app->session->setFlash('error', $errMsg);
                    }
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CompanyWireIn model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CompanyWireIn model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CompanyWireIn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CompanyPayIn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CompanyPayIn::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
