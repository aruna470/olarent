<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Permission */

$this->pageTitle = Yii::t('app', 'View Permission');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'View');

?>
<div class="permission-view">

    <p>
        <?php if (Yii::$app->user->can('Permission.Update')): ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->name], ['class' => 'btn btn-info']) ?>
        <?php endif; ?>

        <?php if (Yii::$app->user->can('Permission.Delete')): ?>
            <?=
            Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->name], [
                'class' => 'btn btn-info',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
        <?php endif; ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'description:ntext',
            'category',
            'createdAt',
            'updatedAt',
            [
                'label' => $model->getAttributeLabel('createdById'),
                'value' => User::getFullNameById($model->createdById),
            ],
            [
                'label' => $model->getAttributeLabel('updatedById'),
                'value' => User::getFullNameById($model->updatedById),
            ],
        ],
    ])
    ?>

</div>
