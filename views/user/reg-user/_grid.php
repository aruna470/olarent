<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Role;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="grid-view-container">
    <div class="table-responsive">
        <?=
        GridView::widget([
            'id' => 'dataGrid',
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class'=>'table table-striped'],
            'columns' => [
                [
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::img($model->getProfileImgThumbnail(), ['width' => 40, 'height' => 40]);
                    }
                ],
                [
                    'attribute' => 'firstName',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return wordwrap($model->firstName, 15, "<br/>", true);
                    }
                ],
                [
                    'attribute' => 'lastName',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return wordwrap($model->lastName, 15, "<br/>", true);
                    }
                ],
                'email:email',
                'phone',
                [
                    'attribute' => 'type',
                    'value' => function ($model) use($userTypes) {
                        return $userTypes[$model->type];
                    }
                ],
                [
                    'attribute' => 'createdAt',
                    'value' => function ($model) {
                        return Yii::$app->util->getLocalDateTime($model->createdAt, Yii::$app->user->identity->timeZone);
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'headerOptions' => ['style' => 'text-align: right'],
                    'contentOptions' => ['style' => 'text-align: right'],
                    'template' => '{view} {update}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Yii::$app->user->can('User.RegUserView') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '#', ['class' => 'view', 'data-url' => Url::to(['user/reg-user-view', 'id' => $model->id])]) : '';
                        },
                        'update' => function ($url, $model, $key) {
                            $return = '';
                            if (Yii::$app->user->can('User.RegUserUpdate')) {
                                $return = Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['user/reg-user-update', 'id' => $model->id]);
                            }
                            return $return;
                        },
                        'delete' => function ($url, $model, $key) {
                            $return = '';
                            if (Yii::$app->user->can('User.RegUserDelete')) {
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
        ]);
        ?>
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
