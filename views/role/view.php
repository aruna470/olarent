<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;
use app\models\Role;

/* @var $this yii\web\View */
/* @var $model app\models\Role */

$this->pageTitle = Yii::t('app', 'View Role');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'View');
?>

<div class="role-view">

    <p>
        <?php if (Yii::$app->user->can('Role.Update')): ?>
            <?php if ($model->name != Role::SUPER_ADMIN && $model->name != Yii::$app->user->identity->roleName): ?>
                <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->name], ['class' => 'btn btn-info']) ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (Yii::$app->user->can('Role.Delete')): ?>
            <?php if ($model->name != Role::SUPER_ADMIN && $model->name != Yii::$app->user->identity->roleName): ?>
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
        <?php endif; ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'description:ntext',
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
            [
				'label' => 'Permissions',
                'format' => 'raw',
				'value' => $permissions,
			],
        ],
    ])
    ?>

</div>
