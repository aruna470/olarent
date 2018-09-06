<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CompanyWireIn */

$this->pageTitle = Yii::t('app', 'Create Pay In');
$this->pageTitleDescription = Yii::t('app', 'Create pay in wire transaction for company wallet.');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company Wallet'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company Pay In'), 'url' => ['company-pay-in/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="company-wire-in-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
