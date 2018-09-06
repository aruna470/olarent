<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PropertySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->pageTitle = Yii::t('app', 'Properties');
$this->pageTitleDescription = Yii::t('app', 'List all properties');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'List');
?>
<div class="property-index">

    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'urlTenant' => $urlTenant,
        'urlOwner' => $urlOwner,
        'ownerName' => $ownerName,
        'tenantName' => $tenantName,
        'statuses' => $statuses
    ]); ?>

    <div class="grid-view-container">
        <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class'=>'table table-striped'],
            'columns' => [
                'code',
                [
                    'attribute' => 'createdAt',
                    'value' => function ($model) {
                        return Yii::$app->util->getLocalDateTime($model->createdAt, Yii::$app->user->identity->timeZone);
                    }
                ],
                'city',
                [
                    'format' => 'raw',
                    'attribute' => 'name',
                    'value' => function ($model) {
                        return wordwrap($model->name, 15, "<br/>", true);
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'ownerUserId',
                    'value' => function ($model) {
                        return wordwrap($model->ownerUser->firstName . " " . $model->ownerUser->lastName, 15, "<br/>", true);
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'tenantUserId',
                    'value' => function ($model) {
                        if (is_object($model->tenantUser)) {
                            return wordwrap($model->tenantUser->firstName . " " . $model->tenantUser->lastName, 15, "<br/>", true);
                        }
                        return '-';
                    }
                ],
                'cost',
                [
                    'attribute' => 'status',
                    'value' => function ($model) use($statuses) {
                        return $statuses[$model->status];
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
                            return Yii::$app->user->can('Property.View') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '#', ['class' => 'view', 'data-url' => Url::to(['property/view', 'id' => $model->id])]) : '';
                        },
                        'update' => function ($url, $model, $key) {
                            $return = '';
                            if (Yii::$app->user->can('Property.Update')) {
                                $return = Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'edit']);
                            }
                            return $return;
                        },
                        'delete' => function ($url, $model, $key) {
                            $return = '';
                            if (Yii::$app->user->can('Property.Delete')) {
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
        util.openFancyboxIframe($(this).attr('data-url'), 700, 480);
        return false;
    });
});
JS;

$this->registerJs($script);
?>