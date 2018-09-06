<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\PropertySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="property-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'name')->textInput(['placeholder' => $model->getAttributeLabel('name')])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'code')->textInput(['placeholder' => $model->getAttributeLabel('code')])->label(false) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'ownerUserId')->widget(Select2::classname(), [
                'initValueText' => $ownerName,
                'options' => ['placeholder' => Yii::t('app','Owner')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => $urlOwner,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    //'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    //'templateResult' => new JsExpression('function(city) { return city.text; }'),
                    //'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                ],
            ])->label(false); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'tenantUserId')->widget(Select2::classname(), [
                'initValueText' => $tenantName,
                'options' => ['placeholder' => Yii::t('app','Tenant')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => $urlTenant,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    //'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    //'templateResult' => new JsExpression('function(city) { return city.text; }'),
                    //'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                ],
            ])->label(false); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'status')->dropDownList($statuses, ['prompt' => Yii::t('app', '- Status -')])->label(false); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-info']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
