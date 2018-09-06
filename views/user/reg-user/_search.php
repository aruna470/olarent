<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use app\models\Role;


/* @var $this yii\web\View */
/* @var $model app\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">
    <?php Pjax::begin(['id' => 'searchPjax']); ?>
    <?php
    $form = ActiveForm::begin([
        'id' => 'searchForm',
        'action' => ['reg-user-index'],
        'method' => 'get'
    ]);
    ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'firstName')->textInput(['placeholder' => $model->getAttributeLabel('firstName')])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'lastName')->textInput(['placeholder' => $model->getAttributeLabel('lastName')])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'email')->textInput(['placeholder' => $model->getAttributeLabel('email')])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'phone')->textInput(['placeholder' => $model->getAttributeLabel('phone')])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'type')->dropDownList($userTypes, ['prompt' => Yii::t('app', '- User Type -')])->label(false); ?>
        </div>
        <div class="col-md-2">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-info']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>

</div>
