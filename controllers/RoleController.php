<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Role;
use app\models\RoleSearch;
use app\models\PermissionSearch;
use app\models\RolePermission;
use app\models\User;

/**
 * RoleController implements the CRUD actions for Role model.
 */
class RoleController extends BaseController
{

    public function behaviors()
    {
        return [
        ];
    }

    /**
     * Lists all Role models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->appLog->writeLog('List Roles');

        $searchModel = new RoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Role model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        Yii::$app->appLog->writeLog('View Role', ['id' => $id]);

        $model = $this->findModel($id);
        $model->modelPermissions = RolePermission::findAll(['roleName' => $model->name]);
        $permissions = '';
        foreach ($model->modelPermissions as $permission) {
            $permissions .= "<p>{$permission->permissionName}</p>";
        }

        return $this->render('view', [
            'model' => $model,
            'permissions' => $permissions
        ]);
    }

    /**
     * Creates a new Role model.
     * @return mixed
     */
    public function actionCreate()
    {
        $sucMsg = Yii::t('app', 'Role created.');
        $errMsg = Yii::t('app', 'Role create failed.');

        $model = new Role();
        $searchModel = new PermissionSearch();
        $dataProvider = $searchModel->search([]);

        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            $model->userPermissions = !empty($postData['Permission']) ? $postData['Permission']: array();
            $allSuccess = true;
            $transaction = Yii::$app->db->beginTransaction();

            if ($model->saveModel()) {
                foreach ($postData['Permission'] as $permission) {
                    $modelRolePermission = new RolePermission();
                    $modelRolePermission->roleName = $model->name;
                    $modelRolePermission->permissionName = $permission;

                    if (!$modelRolePermission->saveModel()) {
                        $allSuccess = false;
                        break;
                    }
                }
            } else {
                if (isset($model->errors['userPermissions'])) {
                    $errMsg = @$model->errors['userPermissions'][0];
                }
                $allSuccess = false;
            }

            if ($allSuccess) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', $sucMsg);
                Yii::$app->appLog->writeLog('Commit transaction');
                return $this->redirect(['index']);
            } else {
                $transaction->rollBack();
                Yii::$app->appLog->writeLog('Rollback transaction');
                Yii::$app->session->setFlash('error', $errMsg);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Update a existing Role model.
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $sucMsg = Yii::t('app', 'Role was successfully updated');
        $errMsg = Yii::t('app', 'Role could not be updated');

        $model = $this->findModel($id);

        // Load Role Permission to Role model
        $modelPermissions = RolePermission::findAll(['roleName' => $model->name]);
        foreach ($modelPermissions as $modelPermission) {
            $model->modelPermissions[] = $modelPermission->permissionName;
        }

        $searchModel = new PermissionSearch();
        $dataProvider = $searchModel->search([]);

        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            $model->userPermissions = !empty($postData['Permission']) ? $postData['Permission'] : array();
            $transaction = Yii::$app->db->beginTransaction();
            $allSuccess = true;

            if ($model->saveModel()) {
                // Deletes existing permission items and re insert
                if (RolePermission::deleteAll(['roleName' => $model->name])) {
                    foreach ($postData['Permission'] as $permission) {
                        $modelRolePermission = new RolePermission();
                        $modelRolePermission->roleName = $model->name;
                        $modelRolePermission->permissionName = $permission;
                        if (!$modelRolePermission->saveModel()) {
                            $allSuccess = false;
                            break;
                        }
                    }
                } else {
                    $allSuccess = false;
                }
            } else {
                if (isset($model->errors['userPermissions'])) {
                    $errMsg = @$model->errors['userPermissions'][0];
                }
                $allSuccess = false;
            }

            if ($allSuccess) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', $sucMsg);
                Yii::$app->appLog->writeLog('Commit transaction');
                return $this->redirect(['index']);
            } else {
                $transaction->rollBack();
                Yii::$app->appLog->writeLog('Rollback transaction');
                Yii::$app->session->setFlash('error', $errMsg);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Deletes an existing Role model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $sucMsg = Yii::t('app', 'Role deleted');
        $errMsg = Yii::t('app', 'Role delete failed');

        $model = $this->findModel($id);

        $user = User::findOne(['roleName' => $model->name]);
        if (empty($user)) {
            $allSuccess = true;
            $transaction = Yii::$app->db->beginTransaction();

            if (RolePermission::deleteAll(['roleName' => $model->name])) {
                if (!$model->deleteModel()) {
                    $allSuccess = false;
                }
            } else {
                $allSuccess = false;
            }

            if ($allSuccess) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', $sucMsg);
                Yii::$app->appLog->writeLog('Commit transaction');
                return $this->redirect(['index']);
            } else {
                $transaction->rollBack();
                Yii::$app->appLog->writeLog('Rollback transaction');
                Yii::$app->session->setFlash('error', $errMsg);
            }
        } else {
            Yii::$app->appLog->writeLog('First delete users associated with this role');
            Yii::$app->session->setFlash('error', 'First delete users associated with this role');
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Role model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Role the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Role::findOne($id)) !== null) {
            return $model;
        } else {
            Yii::$app->appLog->writeLog('The requested page does not exist', ['id' => $id]);
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
