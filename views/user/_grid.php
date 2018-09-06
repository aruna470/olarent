<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Role;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?php
Pjax::begin([
    'id' => 'dataPjax',
    'timeout' => false,
    'enablePushState' => false,
    'clientOptions' => ['method' => 'POST'],
]);
?>
<?=
GridView::widget([
    'id' => 'dataGrid',
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class'=>'table table-striped'],
    'columns' => [
        'username',
        'firstName',
        'lastName',
        'sysEmail:email',
        'timeZone',
        'roleName',
        [
            'attribute' => 'status',
            'filter' => $model->statuses,
            'value' => function ($model) {
                return $model->status === 1 ? Yii::t('app', 'Active') : Yii::t('app', 'InActive');
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => Yii::t('app', 'Actions'),
            'headerOptions' => ['style' => 'text-align: right'],
            'contentOptions' => ['style' => 'text-align: right'],
            'template' => '{view} {update} {delete}',
            'buttons' => [
                'view' => function ($url) {
                    return Yii::$app->user->can('User.View') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url) : '';
                },
                'update' => function ($url, $model, $key) {
                    $return = '';
                    if (Yii::$app->user->can('User.Update')) {
                        $return = Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'edit']);
                        if ($model->roleName == Role::SUPER_ADMIN) {
                            $return = '';
                        } else if ($model->username == Yii::$app->user->identity->username) {
                            $return = '';
                        }
                    }
                    return $return;
                },
                'delete' => function ($url, $model, $key) {
                    $return = '';
                    if (Yii::$app->user->can('User.Delete')) {
                        $return = Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'class' => 'delete',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ]
                        ]);
                        if ($model->roleName == Role::SUPER_ADMIN) {
                            $return = '';
                        } else if ($model->username == Yii::$app->user->identity->username) {
                            $return = '';
                        }
                    }
                    return $return;
                },
            ],
        ],
    ],
]);
?>
<?php Pjax::end(); ?>

