<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->pageTitle = Yii::t('app', 'Unsubscribe');
$this->params['breadcrumbs'][] = Yii::t('app', 'Unsubscribe');
?>
<div class="site-error">
    <div class="alert alert-info">
        <?= nl2br(Yii::t('app', 'You have successfully unsubscribed from Olarent mailing group')) ?>
    </div>
</div>
