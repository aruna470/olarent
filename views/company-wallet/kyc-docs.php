<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyWallet */
/* @var $form yii\widgets\ActiveForm */

$this->pageTitle = Yii::t('app', 'Manage Proof Documents');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company Wallet'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Manage Proof Documents');

?>

<div class="company-wallet-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'idFile')->fileInput(['accept' => 'image/jpeg, image/png, application/pdf', 'class' => 'btn btn-warning', 'id' => 'idFile', 'title' => Yii::t('app', 'Select Identity File (png, jpg, pdf, jpeg)')])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= Html::submitButton(Yii::t('app', 'Upload'), ['class' => 'btn btn-info']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div class="grid-view-container">
    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $provider,
            'tableOptions' => ['class'=>'table table-striped'],
            'columns' => [
                'createdAt',
                'status',
                [
                    'attribute' => 'docType',
                    'label' => Yii::t('app', 'Document Type'),
                    'value' => function ($model) use($docTypes) {
                        return $docTypes[$model['docType']];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'headerOptions' => ['style' => 'text-align: right'],
                    'contentOptions' => ['style' => 'text-align: right'],
                    'template' => '{download}',
                    'buttons' => [
                        'download' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-download"></span>', $model['fileUrl'], ['target' => '_blank']);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>

<?php
$script = <<< JS

$(document).ready(function() {
    $('#idFile').bootstrapFileInput();
});

JS;

$this->registerJs($script);
?>