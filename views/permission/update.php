<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Permission */

$this->pageTitle = Yii::t('app', 'Update Permission');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

?>
<div class="permission-update">

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
