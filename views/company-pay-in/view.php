<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyWireIn */
?>

<div class="panel panel-info">
    <div class="panel-heading"><?= Yii::t('app', 'Wire Transfer Details (Use following details to transfer your money in bank).') ?></div>
    <div class="panel-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'wireReference',
                'ownerName',
                'ownerAddress',
                'bic',
                'iban',
                'amount',
                'currency'
            ],
        ]) ?>
    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('app', 'General Details') ?></div>
    <div class="panel-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'type',
                'status',
                'mpWalletId',
                'mpUserId',
                [
                    'attribute' => 'createdAt',
                    'value' => Yii::$app->util->getLocalDateTime($model->createdAt, Yii::$app->user->identity->timeZone)
                ],
                [
                    'label' => $model->getAttributeLabel('createdById'),
                    'value' => $model->user->getFullName(),
                ],
            ],
        ]) ?>
    </div>
</div>
