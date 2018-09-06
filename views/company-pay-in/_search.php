<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyWireInSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-wire-in-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'wireReference') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'ownerName') ?>

    <?= $form->field($model, 'ownerAddress') ?>

    <?php // echo $form->field($model, 'bic') ?>

    <?php // echo $form->field($model, 'iban') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'currency') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'mpWalletId') ?>

    <?php // echo $form->field($model, 'mpUserId') ?>

    <?php // echo $form->field($model, 'createdAt') ?>

    <?php // echo $form->field($model, 'createdById') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
