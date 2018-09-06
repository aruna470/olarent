<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Property */
?>

<?php if (!empty($imageList)): ?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="img-list">
            <?php
                foreach ($imageList as $image) {
            ?>
                    <div class="item"><?= Html::img($image['imageUrl'], ['width' => '100%', 'height' => '100%'])?></div>
            <?php
                }
            ?>
        </div>

    </div>
</div>
<?php endif; ?>

<div class="panel panel-default">
    <div class="panel-default-custom panel-heading">
        <h3 class="panel-title"><?= Yii::t('app', 'Property Details') ?></h3>
    </div>
    <div class="panel-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'ownerUserId',
                    'value' => "{$model->ownerUser->firstName} {$model->ownerUser->lastName}",
                ],
                [
                    'attribute' => 'tenantUserId',
                    'value' => is_object($model->tenantUser) ? "{$model->tenantUser->firstName} {$model->tenantUser->lastName}" : "-",
                ],
                [
                    'label' => $model->getAttributeLabel('isOnBhf'),
                    'value' => $model->isOnBhf ? Yii::t('app', 'Yes (Tenant created the property)') : Yii::t('app', 'No'),
                ],
                'code',
                'name',
                'description',
                'address',
                'cost',
                'city',
                'zipCode',
                'keyMoney',
                'size',
                'payDay',
                [
                    'attribute' => 'status',
                    'value' => $statuses[$model->status],
                ],
                'currentRentDueAt',
                [
                    'attribute' => 'paymentStatus',
                    'value' => $model->paymentStatuses[$model->paymentStatus],
                ],
                [
                    'attribute' => 'createdAt',
                    'value' => Yii::$app->util->getLocalDateTime($model->createdAt, Yii::$app->user->identity->timeZone)
                ],
                [
                    'attribute' => 'reservedAt',
                    'value' => Yii::$app->util->getLocalDateTime($model->reservedAt, Yii::$app->user->identity->timeZone)
                ],
                [
                    'attribute' => 'nextChargingDate',
                    'value' => Yii::$app->util->getLocalDateTime($model->nextChargingDate, Yii::$app->user->identity->timeZone)
                ],
            ],
        ]) ?>
    </div>
</div>

<?php
$script = <<< JS
$( document ).ready(function() {
    $('.img-list').owlCarousel({
        items:1,
        margin:10,
        nav:false,
        dots:true,
        autoHeight:false
    });
});
JS;

$this->registerJs($script);
?>


