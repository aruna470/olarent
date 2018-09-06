<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CompanyWallet */

$this->pageTitle = Yii::t('app', 'Create Wallet');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company Wallet'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="company-wallet-create">

    <?= $this->render('_form', [
        'model' => $model,
        'incomeRanges' => $incomeRanges,
        'nationalities' => $nationalities,
        'countryCodes' => $countryCodes
    ]) ?>

</div>
