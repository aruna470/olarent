<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->pageTitle = Yii::t('app', 'Payments');
$this->pageTitleDescription = Yii::t('app', 'List all payments');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'List');
?>
<div class="payment-index">

    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'urlTenant' => $urlTenant,
        'urlOwner' => $urlOwner,
        'ownerName' => $ownerName,
        'tenantName' => $tenantName
    ]); ?>

    <div class="grid-view-container">
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class'=>'table table-striped'],
                'columns' => [
                    [
                        'attribute' => 'payerUserId',
                        'label' => Yii::t('app', 'Tenant'),
                        'value' => function ($model) {
                            return $model->payerUser->getFullName();
                        }
                    ],
                    [
                        'attribute' => 'payeeUserId',
                        'label' => Yii::t('app', 'Owner'),
                        'value' => function ($model) {
                            return $model->payeeUser->getFullName();
                        }
                    ],
                    [
                        'attribute' => 'propertyId',
                        'label' => Yii::t('app', 'Property Name'),
                        'value' => function ($model) {
                            return "{$model->property->name}";
                        }
                    ],
                    [
                        'attribute' => 'propertyCode',
                        'label' => Yii::t('app', 'Property Code'),
                        'value' => function ($model) {
                            return "{$model->property->code}";
                        }
                    ],
                    'amount',
                    'commssion',
                    [
                        'attribute' => 'type',
                        'value' => function ($model) {
                            return $model->paymentTypes[$model->type];
                        }
                    ],
                    'createdAt'
                ],
            ]); ?>
        </div>
    </div>
</div>
