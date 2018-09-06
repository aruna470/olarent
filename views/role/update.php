<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Role */

$this->pageTitle = Yii::t('app', 'Update Role');
$this->pageTitleDescription = Yii::t('app', 'Update role');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="role-update">

    <?=
    $this->render('_form', [
        'model' => $model,
		'dataProvider' => $dataProvider
    ])
    ?>

</div>
