<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Role */

$this->pageTitle = Yii::t('app', 'Create Role');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>

<div class="role-create">

    <?=
    $this->render('_form', [
        'model' => $model,
		'dataProvider' => $dataProvider
    ])
    ?>

</div>
