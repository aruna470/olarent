<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RoleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="role-search">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);
    ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'description') ?>

    <?php //echo $form->field($model, 'createdAt') ?>

    <?php //echo $form->field($model, 'updatedAt') ?>

    <?php //echo $form->field($model, 'createdById') ?>

    <?php //echo $form->field($model, 'updatedById')  ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-info']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-info']) ?>
    </div>

    <!--
    <div class="form-group">
        <?php //echo Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])  ?>
        <?php //echo Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default'])  ?>
    </div>
    -->

    <?php ActiveForm::end(); ?>

</div>
