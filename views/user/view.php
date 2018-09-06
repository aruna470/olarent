<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Role;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->pageTitle = Yii::t('app', 'View User');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'View');
?>

<div class="user-view">

    <p>
        <?php if (Yii::$app->user->can('User.Update') && $model->roleName !== Role::SUPER_ADMIN && $model->username !== Yii::$app->user->identity->username): ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?php endif; ?>

        <?php if (Yii::$app->user->can('User.Delete') && $model->roleName !== Role::SUPER_ADMIN && $model->username !== Yii::$app->user->identity->username): ?>
            <?=
            Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
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
            'username',
            'firstName',
            'lastName',
            'sysEmail:email',
            'timeZone',
            'roleName',
            [
                'label' => $model->getAttributeLabel('status'),
                'value' => $model->statuses[$model->status],
            ],
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
