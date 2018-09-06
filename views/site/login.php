<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */
$this->title = 'Login';
?>
<div class="login-title"><img class="login-logo" src="<?= Yii::$app->view->theme->baseUrl ?>/img/olarent-logo.png" alt="<?php echo Yii::t('app', '{name}', ['name' => Yii::$app->params['productName']]); ?>"></div>

<?php
$form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => [],
        'fieldConfig' => [],
    ]);
?>

<?= $form->field($model, 'username') ?>

<?= $form->field($model, 'password')->passwordInput() ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-info btn-block btn-lg', 'name' => 'login-button']) ?>
    <?php //echo Html::a(Yii::t('app', 'Forgot Password'), ['user/forgot-password'], ['class' => 'btn btn-info btn-block']) ?>
</div>

<?php ActiveForm::end(); ?>