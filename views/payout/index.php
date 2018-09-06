<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PayoutSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->pageTitle = Yii::t('app', 'Payouts');
$this->pageTitleDescription = Yii::t('app', 'List all payouts');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payouts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'List');
?>
<div class="payout-index">

    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'urlOwner' => $urlOwner,
        'ownerName' => $ownerName,
    ]); ?>

    <div class="grid-view-container">
        <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class'=>'table table-striped'],
            'columns' => [
                'createdAt',
                [
                    'attribute' => 'userId',
                    'value' => function ($model) {
                        return $model->user->getFullName();
                    }
                ],
                [
                    'label' => Yii::t('app', 'Property Code'),
                    'value' => function ($model) {
                        return $model->payment->property->code;
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'eligibilityStatus',
                    'value' => function ($model) {
                        $statusLabel = $model::ES_SUCCESS == $model->eligibilityStatus ? Yii::t('app', 'Success') : Yii::t('app', 'Failed');
                        return Html::a($statusLabel, '#', ['data-toggle' => 'popover', 'class' => 'popup-msg',
                            'data-content' => $model->getEligibilityDescription(), 'title' => Yii::t('app', 'Description')]);
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'mpTransferStatus',
                    'value' => function ($model) {
                        if ($model->mpTransferStatus != '') {
                            return Html::a($model->mpTransferStatus, '#', ['data-toggle' => 'popover', 'class' => 'popup-msg',
                                'data-content' => $model->getTransferDescription(), 'title' => Yii::t('app', 'Transfer Message')]);
                        }
                        return '-';
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'mpPayoutStatus',
                    'value' => function ($model) {
                        if ($model->mpPayoutStatus != '') {
                            return Html::a($model->mpPayoutStatus, '#', ['data-toggle' => 'popover', 'class' => 'popup-msg',
                                'data-content' => $model->getPayoutDescription(), 'title' => Yii::t('app', 'Payout Message')]);
                        }
                        return '-';
                    }
                ],
                [
                    'label' => Yii::t('app', 'Amount ({currency})', ['currency' => Yii::$app->params['defCurrency']]),
                    'value' => function ($model) {
                        return $model->payment->amount;
                    }
                ],
                [
                    'attribute' => 'retryCount',
                    'value' => function ($model) {
                        return "{$model->retryCount}/{$model->maxRetry}";
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'headerOptions' => ['style' => 'text-align: right'],
                    'contentOptions' => ['style' => 'text-align: right'],
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Yii::$app->user->can('Payout.View') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '#', ['class' => 'view', 'data-url' => Url::to(['payout/view', 'id' => $model->id])]) : '';
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
    $("document").ready(function() {
        $('[data-toggle="popover"]').popover();

        $('.popup-msg').on('click', function(e) {e.preventDefault(); return true;});

        $(document).on('click', '.view', function(e) {
            util.openFancyboxIframe($(this).attr('data-url'), 700, 480);
            return false;
        });
    });
JS;

$this->registerJs($script);
?>




