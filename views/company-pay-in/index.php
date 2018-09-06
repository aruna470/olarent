<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CompanyPayInSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->pageTitle = Yii::t('app', 'Wire In Transactions');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company Wallet'), 'url' => ['company-wallet/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pay In Transactions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'List');
?>
<div class="company-wire-in-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (Yii::$app->user->can('CompanyPayIn.Create')): ?>
        <p><?= Html::a(Yii::t('app', 'Create Company Pay In'), ['create'], ['class' => 'btn btn-info']) ?></p>
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped'],
        'columns' => [
            'wireReference',
            'bic',
            'iban',
            'amount',
            'status',
            [
                'attribute' => 'createdAt',
                'value' => function ($model) {
                    return Yii::$app->util->getLocalDateTime($model->createdAt, Yii::$app->user->identity->timeZone);
                }
            ],
            [
                'attribute' => 'createdById',
                'value' => function ($model) {
                    return $model->user->getFullName();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Actions'),
                'headerOptions' => ['style' => 'text-align: right'],
                'contentOptions' => ['style' => 'text-align: right'],
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url) {
                        return Yii::$app->user->can('CompanyWallet.View') ?
                            Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '#',
                                ['class' => 'view', 'data-url' => $url]) : '';
                    },
                ],
            ],
        ],
    ]); ?>

</div>

<?php
$script = <<< JS
$( document ).ready(function() {
    $(document).on('click', '.view', function(e) {
        util.openFancyboxIframe($(this).attr('data-url'), 700, 443);
        return false;
    });
});
JS;

$this->registerJs($script);
?>