<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyWallet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-wallet-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'birthDate')->widget(DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
                'options' => [
                    'id' => 'dob',
                    'class' => 'form-control dob',
                    'readOnly' => true,
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'showButtonPanel' => true,
                    'yearRange' => '-100:+0'
                ],
            ]);
            ?>
            <?= $form->field($model, 'incomeRange')->dropDownList($incomeRanges, ['prompt' => Yii::t('app', '- Income Range -')]);?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'firstName')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'nationality')->dropDownList($nationalities, ['prompt' => Yii::t('app', '- Nationality -')]);?>
            <?= $form->field($model, 'occupation')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'lastName')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'countryOfResidence')->dropDownList($countryCodes, ['prompt' => Yii::t('app', '- Country -')]);?>
            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-info']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
