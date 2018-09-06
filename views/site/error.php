<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Yii::t('app', 'Error')?></h1>

    <div class="alert alert-danger">
        <?= Yii::t('app', 'An error occur while processing the request. Please try again later.') ?>
    </div>

</div>
