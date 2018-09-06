<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyWallet */
?>
<div class="company-wallet-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'format' => 'raw',
                'label' => Yii::t('app', 'Balance ({currency})', ['currency' => Yii::$app->params['defCurrency']]),
                'value' => '<h4><span class="label label-success">' . $balance . '</span></h4>',
            ],
            'email:email',
            'firstName',
            'lastName',
            'birthDate',
            [
                'label' => $model->getAttributeLabel('nationality'),
                'value' => $nationalities[$model->nationality],
            ],
            [
                'label' => $model->getAttributeLabel('countryOfResidence'),
                'value' => $countries[$model->countryOfResidence],
            ],
            [
                'label' => $model->getAttributeLabel('incomeRange'),
                'value' => $incomeRanges[$model->incomeRange],
            ],
            'occupation',
            'createdAt',
            'updatedAt',
            'mpUserId',
            'mpWalletId',
        ],
    ]) ?>

</div>
