<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->pageTitle = Yii::t('app', 'Access Denied');
//$this->pageTitleDescription = Yii::t('app', 'Access denied');
$this->params['breadcrumbs'][] = Yii::t('app', 'Error');
?>
<div class="site-error">
    <div class="alert alert-danger">
        <?= nl2br(Yii::t('app', 'You do not have permission to access this feature. Please contact your system administrator.')) ?>
    </div>
</div>
