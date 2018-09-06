<?php

namespace app\controllers;


use Yii;
use app\models\CompanyWallet;
use app\models\CompanyWalletSearch;
use app\components\Aws;
use app\components\Mp;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;

/**
 * CompanyWalletController implements the CRUD actions for CompanyWallet model.
 */
class CompanyWalletController extends BaseController
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
     * Lists all CompanyWallet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CompanyWalletSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mp' => new Mp(Yii::$app->params['mangoPay'])
        ]);
    }

    /**
     * Displays a single CompanyWallet model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $mp = new Mp(Yii::$app->params['mangoPay']);
        $res = $mp->getWallet($model->mpWalletId);

        $this->layout = 'popup';
        return $this->render('view', [
            'model' => $model,
            'nationalities' => $mp->getNationalities(),
            'incomeRanges' => $mp->getIncomeRanges(),
            'countries' => $mp->getCountryCodes(),
            'balance' => $res->Balance->Amount/100
        ]);
    }

    /**
     * Creates a new CompanyWallet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $sucMsg = Yii::t('app', 'Company wallet created');
        $errMsg = Yii::t('app', 'Company wallet create failed.');

        $model = new CompanyWallet();
        $model->scenario = CompanyWallet::SCENARIO_CREATE;
        $mp = new Mp(Yii::$app->params['mangoPay']);
        $status = false;

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->saveModel()) {
                $model->isNewRecord = false;
                $userRes = $mp->createNaturalUser($model->attributes);
                if (isset($userRes->Id)) {
                    $model->mpUserId = $userRes->Id;
                    $walletRes = $mp->createWallet($model->mpUserId, Yii::$app->params['defCurrency'], 'Company wallet');
                    if (isset($walletRes->Id)) {
                        $model->mpWalletId = $walletRes->Id;
                        if ($model->saveModel()) {
                            $status = true;
                        }
                    }
                }
            }

            if ($status) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', $sucMsg);
                Yii::$app->appLog->writeLog('Transaction committed.');
                return $this->redirect(['index']);
            } else {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $errMsg);
                Yii::$app->appLog->writeLog('Transaction rollbacked.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'incomeRanges' => $mp->getIncomeRanges(),
            'nationalities' => $mp->getNationalities(),
            'countryCodes' => $mp->getCountryCodes()
        ]);
    }

    /**
     * Updates an existing CompanyWallet model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $sucMsg = Yii::t('app', 'Company wallet updated');
        $errMsg = Yii::t('app', 'Company wallet update failed.');

        $model = $this->findModel($id);
        $model->scenario = CompanyWallet::SCENARIO_UPDATE;
        $mp = new Mp(Yii::$app->params['mangoPay']);
        $status = false;

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->saveModel()) {
                $userRes = $mp->updateNaturalUser($model->attributes);
                if (isset($userRes->Id)) {
                    $status = true;
                }
            }

            if ($status) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', $sucMsg);
                Yii::$app->appLog->writeLog('Transaction committed.');
                return $this->redirect(['index']);
            } else {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $errMsg);
                Yii::$app->appLog->writeLog('Transaction rollbacked.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'incomeRanges' => $mp->getIncomeRanges(),
            'nationalities' => $mp->getNationalities(),
            'countryCodes' => $mp->getCountryCodes()
        ]);
    }

    /**
     * Add KYC documents.
     * @param integer $id
     * @return mixed
     */
    public function actionManageKycDocs($id)
    {
        $sucMsg = Yii::t('app', 'Document submitted');
        $errMsg = Yii::t('app', 'Document submit failed.');

        $aws = new Aws();
        $mp = new Mp(Yii::$app->params['mangoPay']);
        $model = $this->findModel($id);
        $model->scenario = CompanyWallet::KYC_DOCUMENT_CREATE;

        if (Yii::$app->request->post()) {
            $model->idFile = UploadedFile::getInstance($model, 'idFile');
            if ($model->validateModel()) {
                $fileName = $model->getCompIdFileName($model->idFile->name);
                $res = $aws->s3UploadObject($fileName, $model->idFile->tempName);
                if ('' != $res['ObjectURL']) {
                    $resDoc = $mp->createKycDocument($model->mpUserId, Mp::IDENTITY_PROOF);
                    if (isset($resDoc->Id)) {
                        $fileContent = file_get_contents($model->idFile->tempName);
                        $resUpload = $mp->uploadKycDocument($model->mpUserId, $resDoc->Id, base64_encode($fileContent));
                        if ($resUpload) {
                            $mp->updateKycDocument($model->mpUserId, $resDoc->Id);
                            $newList = $model->addKycDocument(json_decode($model->kycDocuments), $fileName, $resDoc->Id, Yii::$app->util->getUtcDateTime());
                            $model->kycDocuments = json_encode($newList);
                            if ($model->saveModel()) {
                                Yii::$app->session->setFlash('success', $sucMsg);
                            } else {
                                Yii::$app->session->setFlash('error', $errMsg);
                            }
                        }
                    }
                }
            }
        }

        $provider = new ArrayDataProvider([
            'allModels' => $model->getKycDocuments(json_decode($model->kycDocuments, true), $model->mpUserId, $mp),
        ]);

        return $this->render('kyc-docs', [
            'model' => $model,
            'provider' => $provider,
            'docTypes' => $model->getDocTypes()
        ]);
    }

    /**
     * Add KYC documents.
     * @param integer $id
     * @return mixed
     */
    /*public function actionManageKycDocs($id)
    {
        $sucMsg = Yii::t('app', 'Document submitted');
        $errMsg = Yii::t('app', 'Document submit failed.');

        $aws = new Aws();
        $mp = new Mp(Yii::$app->params['mangoPay']);
        $model = $this->findModel($id);
        $model->scenario = CompanyWallet::KYC_DOCUMENT_CREATE;

        if (Yii::$app->request->post()) {
            $model->idFile = UploadedFile::getInstance($model, 'idFile');
            if ($model->validateModel()) {
                $fileName = $model->getCompIdFileName($model->idFile->name);
                $res = $aws->s3UploadObject($fileName, $model->idFile->tempName);
                if ('' != $res['ObjectURL']) {
                    $docId = $model->getDocId(json_decode($model->kycDocuments), Mp::IDENTITY_PROOF);
                    if ($docId == null) {
                        $resDoc = $mp->createKycDocument($model->mpUserId, Mp::IDENTITY_PROOF);
                        $docId = @$resDoc->Id;
                    }
                    if ($docId != null) {
                        $fileContent = file_get_contents($model->idFile->tempName);
                        $resUpload = $mp->uploadKycDocument($model->mpUserId, $docId, base64_encode($fileContent));
                        print_r($resUpload);
                        if ($resUpload) {
                            $mp->updateKycDocument($model->mpUserId, $docId);
                            $newList = $model->addKycDocument(json_decode($model->kycDocuments), $fileName, $docId, Yii::$app->util->getUtcDateTime());
                            $model->kycDocuments = json_encode($newList);
                            if ($model->saveModel()) {
                                Yii::$app->session->setFlash('success', $sucMsg);
                            } else {
                                Yii::$app->session->setFlash('error', $errMsg);
                            }
                        } else {
                            echo 'xxx';
                            Yii::$app->session->setFlash('error', $errMsg);
                        }
                    } else {
                        echo '1';
                        Yii::$app->session->setFlash('error', $errMsg);
                    }
                } else {
                    echo '2';
                    Yii::$app->session->setFlash('error', $errMsg);
                }
            }
        }

        $provider = new ArrayDataProvider([
            'allModels' => $model->getKycDocuments(json_decode($model->kycDocuments, true), $model->mpUserId, $mp),
        ]);

        return $this->render('kyc-docs', [
            'model' => $model,
            'provider' => $provider,
            'docTypes' => $model->getDocTypes()
        ]);
    }*/

    /**
     * Deletes an existing CompanyWallet model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $sucMsg = Yii::t('app', 'Company wallet deleted.');
        $errMsg = Yii::t('app', 'Company wallet delete failed.');

        $model = $this->findModel($id);
        if ($model->deleteModel()) {
            Yii::$app->session->setFlash('success', $sucMsg);
        } else {
            Yii::$app->session->setFlash('error', $errMsg);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the CompanyWallet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CompanyWallet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CompanyWallet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
