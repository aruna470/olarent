<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PermissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->pageTitle = Yii::t('app', 'Permissions');
$this->pageTitleDescription = Yii::t('app', 'Listing all permissions');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'List');;
?>

<div class="permission-index">

    <?php if (Yii::$app->user->can('Permission.Create')): ?>
        <p> <?= Html::a(Yii::t('app', 'Create Permission'), ['create'], ['class' => 'btn btn-info']) ?></p>
    <?php endif; ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped'],
        'columns' => [
            'name',
            'description:ntext',
            'category',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Actions'),
                'headerOptions' => ['style' => 'text-align: right'],
                'contentOptions' => ['style' => 'text-align: right'],
                'template' => '{update} {delete}',
                'buttons' => [
                    /*'view' => function ($url, $model) {
                        return Yii::$app->user->can('Permission.View') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['permission/view', 'name' => $model->name]) : '';
                    },*/
                    'update' => function ($url, $model) {
                        return Yii::$app->user->can('Permission.Update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['permission/update', 'name' => $model->name]) : '';
                    },
                    'delete' => function ($url, $model) {
                        return Yii::$app->user->can('Permission.Delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', ['permission/delete', 'name' => $model->name], [
                            'class' => 'delete',
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ]
                        ]) : '';
                    },
                ],
            ],
        ],
    ]);
    ?>

</div>
