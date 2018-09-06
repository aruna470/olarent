<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

<?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'firstName') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'lastName') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'sysEmail')->textInput(['readonly' => true]) ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'timeZone')->widget(Select2::classname(), [
                'data' => Yii::$app->util->getTimeZoneList(),
                'language' => 'en',
                'options' => ['placeholder' => Yii::t('app', '- TimeZone -')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-info']) ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
