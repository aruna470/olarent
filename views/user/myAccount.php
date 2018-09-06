<?php

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('app', 'My Account');

$this->pageTitle = Yii::t('app', 'My Account');
$this->pageTitleDescription = Yii::t('app', 'Update your profile details');
$this->params['breadcrumbs'][] = Yii::t('app', 'My Account');
?>

<?php
	echo $this->render('_myAccount', [
		'model' => $model,
	]);
?>
