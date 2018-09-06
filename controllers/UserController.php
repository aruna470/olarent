<?php

namespace app\controllers;

use app\components\Mp;
use app\models\UserMpInfo;
use app\models\UserMpInfoFile;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use yii\web\Response;
use app\models\File;
use app\models\User;
use app\models\UserSearch;


/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{

    public function behaviors()
    {
        return [

        ];
    }

    public function allowed()
    {
        return ['User.GetUsersByType'];
    }

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->appLog->writeLog('List Users');

        $searchModel = new UserSearch();

        $params = (Yii::$app->request->isGet ? Yii::$app->request->queryParams : (Yii::$app->request->isPost ? Yii::$app->request->bodyParams : array()));
        $params['UserSearch']['type'] = User::SYSTEM;

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        Yii::$app->appLog->writeLog('View User', ['id' => $id]);
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $sucMsg = Yii::t('app', 'User created.');
        $errMsg = Yii::t('app', 'User create failed.');

        $model = new User();
        $model->scenario = User::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post())) {
            $model->password = $model->encryptPassword($model->formPassword);
            $model->type = User::SYSTEM;
            if ($model->saveModel()){
                Yii::$app->session->setFlash('success', $sucMsg);
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
        } else {
			$model->status = User::ACTIVE;
			$model->timeZone = Yii::$app->params['defaultTimeZone'];
		}

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $sucMsg = Yii::t('app', 'User updated.');
        $errMsg = Yii::t('app', 'User update failed.');

        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE;
        $curPassword = $model->password;

        if ($model->load(Yii::$app->request->post())) {
            if ('' == $model->formPassword) {
                $model->password = $curPassword;
            } else {
                $model->password = $model->encryptPassword($model->formPassword);
            }

            if ($model->saveModel()){
                Yii::$app->session->setFlash('success', $sucMsg);
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $sucMsg = Yii::t('app', 'User was successfully deleted.');
        $errMsg = Yii::t('app', 'User could not be deleted.');

        $model = $this->findModel($id);
        if ($model->deleteModel()) {
            Yii::$app->session->setFlash('success', $sucMsg);
        } else {
            Yii::$app->session->setFlash('error', $errMsg);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            Yii::$app->appLog->writeLog('The requested page does not exist', ['id' => $id]);
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Update own profile details.
     * @return mixed
     */
    public function actionMyAccount()
    {
        $sucMsg = Yii::t('app', 'Profile updated.');
        $errMsg = Yii::t('app', 'Profile update failed.');

		$id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_MY_ACCOUNT;

		if ($model->load(Yii::$app->request->post())) {
            if ($model->saveModel()){
                Yii::$app->session->setFlash('success', $sucMsg);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
        }

		return $this->render('myAccount', [
			'model' => $model,
		]);
    }

    /**
     * Change password.
     * @return mixed
     */
    public function actionChangePassword()
    {
        $sucMsg = Yii::t('app', 'Password changed.');
        $errMsg = Yii::t('app', 'Password changed failed.');

        $id = Yii::$app->user->identity->id;
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_CHANGE_PASSWORD;
        $model->curOldPassword = $model->password;

        if ($model->load(Yii::$app->request->post())) {

            $oldPassword = '';
            if ('' != $model->oldPassword) {
                $oldPassword = $model->oldPassword;
                $model->oldPassword = User::getComparingPassword($model->oldPassword, $model->curOldPassword);
            }

            $model->password = $model->encryptPassword($model->newPassword);

            if ($model->saveModel()){
                Yii::$app->session->setFlash('success', $sucMsg);
                return $this->redirect(['change-password']);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }

            $model->oldPassword = $oldPassword;
        }

        return $this->render('changePassword', [
            'model' => $model,
        ]);
    }

    /**
     * Reset advertiser password and email
     * @param integer $id Advertiser id
     * @return mixed
     */
    public function actionForgetPassword($id)
    {
		$model = $this->findModel($id);
		$newPassword = $model->getNewPassword();
		$encryptedPw = $model->encryptPassword($newPassword);

		$model->password = $encryptedPw;

		try {
			if ($model->save()) {

				Yii::$app->session->setFlash('success', Yii::t('app', 'Password reset success.'));

				$message = Yii::t('app', 'Dear {name}, Your {productName} password has been reset. New password is:{newPassword}', [
					'name' => $model->firstName,
					'productName' => Yii::$app->params['productName'],
					'newPassword' => $newPassword
				]);

				$response = Yii::$app->mailer
                    ->compose('@app/views/email-template/notificationTemplate', ['content' => $message])
					->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->params['productName']])
					->setTo($model->email)
					->setSubject(Yii::t('app', '{name} Reset Password', ['name' => Yii::$app->params['productName']]))
					->send();

				if (!$response) {
					Yii::$app->session->setFlash('error', Yii::t('app', 'Email sending failed. Try again later.'));
				}
			} else {
				Yii::$app->session->setFlash('error', Yii::t('app', 'Password reset failed.'));
			}
		} catch (Exception $e) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'Password reset failed.'));
		}

        return $this->redirect(['advertiser']);
    }

    /**
     * Lists all registered users.
     * @return mixed
     */
    public function actionRegUserIndex()
    {
        Yii::$app->appLog->writeLog('List Registered users');

        $model = new User();
        $searchModel = new UserSearch();

        $params = (Yii::$app->request->isGet ? Yii::$app->request->queryParams : (Yii::$app->request->isPost ? Yii::$app->request->bodyParams : array()));

        $dataProvider = $searchModel->regUserSearch($params);

        return $this->render('reg-user/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userTypes' => $model->getTypeList()
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionRegUserView($id)
    {
        $this->layout = 'popup';
        $file = new File();
        $model = $this->findModel($id);
        $mp = new Mp(Yii::$app->params['mangoPay']);
        $userMpInfoFile = new UserMpInfoFile();
        $userMpInfo = new UserMpInfo();

        Yii::$app->appLog->writeLog('View User', ['id' => $id]);

        $userMpInfo = $userMpInfo->getUserMpInfo($model->id);
        if (empty($userMpInfo)) {
            $userMpInfo = new UserMpInfo();
        }

        $provider = new ArrayDataProvider([
            'allModels' => $file->getFileList($id),
            'pagination' => false,
        ]);

        $userMpInfoFile->userId = $id;
        $mpFileProvider = new ArrayDataProvider([
            'allModels' => $userMpInfoFile->getFiles(),
            'pagination' => false,
        ]);

        return $this->render('reg-user/view', [
            'model' => $model,
            'userTypes' => $model->getTypeList(),
            'provider' => $provider,
            'companyTypes' => $model->getCompanyTypeList(),
            'userMpInfo' => $userMpInfo,
            'nationalities' => $mp->getNationalities(),
            'countries' => $mp->getCountryCodes(),
            'incomeRanges' => $mp->getIncomeRanges(),
            'mpFileProvider' => $mpFileProvider
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRegUserUpdate($id)
    {
        $sucMsg = Yii::t('app', 'User updated.');
        $errMsg = Yii::t('app', 'User update failed.');

        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_REG_USER_UPDATE;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveModel()){
                Yii::$app->session->setFlash('success', $sucMsg);
                return $this->redirect(['user/reg-user-index']);
            } else {
                Yii::$app->session->setFlash('error', $errMsg);
            }
        }

        return $this->render('reg-user/update', [
            'model' => $model,
        ]);
    }

    /**
     * Retrieve users by user type
     * @param integer $type User type owner or tenant
     * @param string $q Search query
     * @return mixed
     */
    public function actionGetUsersByType($type, $q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];

        if (!is_null($q)) {
            $query = User::find();
            $query->andWhere('type = :type', [':type' => $type]);
            $query->andFilterWhere([
                'or',
                ['like', 'firstName', $q],
                ['like', 'lastName', $q],
            ]);
            $query->limit(5);

            $users = $query->all();

            if (!empty($users)) {
                foreach ($users as $user) {
                    $response['results'][] = ['id' => $user->id, 'text' => "{$user->firstName} {$user->lastName}"];
                }
            }
        }

        if (empty($response)) {
            $response['results'][] = ['id' => '', 'text' => ''];
        }

        return $response;

    }
}

