<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->pageTitle = Yii::t('app', 'Users');
$this->pageTitleDescription = Yii::t('app', 'Tenants and Owners');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['reg-user-index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'List');
?>
<?php
$this->registerJs(
   '$("document").ready(function(){
        $("#searchPjax").on("pjax:end", function() {
            $.pjax.reload({container: "#dataPjax"});  // Reload GridView
        });
    });'
);
?>
<div class="user-index">
    <?php echo $this->render('_search', ['model' => $searchModel, 'userTypes' => $userTypes]); ?>
    <?php echo $this->render('_grid', ['model' => $searchModel, 'dataProvider' => $dataProvider, 'userTypes' => $userTypes]); ?>
</div>
