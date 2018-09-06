<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CompanyWalletSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->pageTitle = Yii::t('app', 'Company Wallet');
$this->pageTitleDescription = Yii::t('app', 'Company\'s MangoPay Wallet');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company Wallet'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'List');
?>
<div class="company-wallet-index">

    <?php if (Yii::$app->user->can('CompanyWallet.Create') && $dataProvider->getCount() == 0): ?>
    <p><?= Html::a(Yii::t('app', 'Create Company Wallet'), ['create'], ['class' => 'btn btn-info']) ?></p>
    <?php endif; ?>

    <?php if (Yii::$app->user->can('CompanyPayIn.Create') && $dataProvider->getCount() != 0): ?>
        <p><?= Html::a(Yii::t('app', 'Pay In'), ['company-pay-in/index'], ['class' => 'btn btn-info']) ?></p>
    <?php endif; ?>

    <div class="grid-view-container">
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class'=>'table table-striped'],
                'columns' => [
                    'email:email',
                    [
                        'format' => 'raw',
                        'label' => Yii::t('app', 'Balance ({currency})', ['currency' => Yii::$app->params['defCurrency']]),
                        'value' => function ($model) use ($mp) {
                            $res = $mp->getWallet($model->mpWalletId);
                            return '<h4><span class="label label-success">' . ($res->Balance->Amount/100) . '</span></h4>';
                        }
                    ],
                    'firstName',
                    'lastName',
                    'birthDate',
                    'createdAt',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => Yii::t('app', 'Actions'),
                        'headerOptions' => ['style' => 'text-align: right'],
                        'contentOptions' => ['style' => 'text-align: right'],
                        'template' => '{kyc} {view} {update}',
                        'buttons' => [
                            'kyc' => function ($url, $model) {
                                return Yii::$app->user->can('CompanyWallet.ManageKycDocs') ?
                                    Html::a('<span class="glyphicon glyphicon-file"></span>', ['manage-kyc-docs', 'id' => $model->id], ['title' => 'Manage Proof Documents']) : '';
                            },
                            'view' => function ($url) {
                                return Yii::$app->user->can('CompanyWallet.View') ?
                                    Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '#',
                                        ['class' => 'view', 'data-url' => $url]) : '';
                            },
                            'update' => function ($url, $model, $key) {
                                $return = '';
                                if (Yii::$app->user->can('CompanyWallet.Update')) {
                                    $return = Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'edit']);
                                }
                                return $return;
                            },
                            'delete' => function ($url, $model, $key) {
                                $return = '';
                                if (Yii::$app->user->can('CompanyWallet.Delete')) {
                                    $return = Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                        'class' => 'delete',
                                        'data' => [
                                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                            'method' => 'post',
                                        ]
                                    ]);
                                }
                                return $return;
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
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