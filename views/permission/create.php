<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Permission */

$this->title = Yii::t('app', 'Create Permission');

$this->pageTitle = Yii::t('app', 'Create Permission');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="permission-create">

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
