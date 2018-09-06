<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Payout */
?>

<div class="payout-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'userId',
                'value' => $model->user->getFullName()
            ],
            [
                'label' => Yii::t('app', 'Mp User Id'),
                'value' => $model->userMpInfo->mpUserId
            ],
            [
                'label' => Yii::t('app', 'Mp Wallet Id'),
                'value' => $model->userMpInfo->mpWalletId
            ],
            'mpTransferId',
            'mpTransferStatus',
            'mpTransferMessage:ntext',
            'mpPayoutId',
            'mpPayoutStatus',
            'mpPayoutMessage:ntext',
            'mpBankAccountId',
            'mpPayoutExecutionDate',
            'retryCount',
            [
                'format' => 'raw',
                'label' => Yii::t('app', 'Eligibility Description'),
                'value' => wordwrap($model->getEligibilityDescription(), 30, "<br/>", true)
            ],
            'createdAt',
        ],
    ]) ?>

</div>
