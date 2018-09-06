<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Role;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->pageTitle = Yii::t('app', 'Roles');
$this->pageTitleDescription = Yii::t('app', 'Listing all roles');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'List');
?>
<div class="role-index">

    <?php if (Yii::$app->user->can('Role.Create')): ?>
        <p>
            <?= Html::a(Yii::t('app', 'Create Role'), ['create'], ['class' => 'btn btn-info']) ?>
        </p>
    <?php endif; ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped'],
        'columns' => [
            'name',
            'description:ntext',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Actions'),
                'headerOptions' => ['style' => 'text-align: right'],
                'contentOptions' => ['style' => 'text-align: right'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url) {
                        return Yii::$app->user->can('Role.View') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url) : '';
                    },
                    'update' => function ($url, $model, $key) {
                        $return = '';
                        if (Yii::$app->user->can('Role.Update')) {
                            $return = Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                            if ($model->name == Role::SUPER_ADMIN) {
                                $return = '';
                            } else if ($model->name == Yii::$app->user->identity->roleName) {
                                $return = '';
                            }
                        }
                        return $return;
                    },
                    'delete' => function ($url, $model, $key) {
                        $return = '';
                        if (Yii::$app->user->can('Role.Delete')) {
                            $return = Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'class' => 'delete',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ]
                            ]);
                            if ($model->name == Role::SUPER_ADMIN) {
                                $return = '';
                            } else if ($model->name == Yii::$app->user->identity->roleName) {
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

</div>
