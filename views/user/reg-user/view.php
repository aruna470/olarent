<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Role;
use app\models\User;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $model app\models\UserMpInfo */

$this->pageTitle = Yii::t('app', 'View User');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'View');
?>

<style>
    .grid-view > table > thead {
        background: #FFFFFF;
    }

    .grid-view > table > thead > tr > th {
        color: #000000;
    }
</style>

<div class="panel panel-default">
    <div class="panel-default-custom panel-heading">
        <h3 class="panel-title"><?= Yii::t('app', 'Profile Details') ?></h3>
    </div>
    <div class="panel-body">
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => $model->getAttributeLabel('type'),
                    'value' => $userTypes[$model->type],
                ],
                [
                    'label' => $model->getAttributeLabel('isOnBhf'),
                    'value' => $model->isOnBhf ? Yii::t('app', 'Yes (Tenant created the owner)') : Yii::t('app', 'No'),
                ],
                'firstName',
                'lastName',
                'email:email',
                'phone',
                'dob',
                'profDes',
                [
                    'label' => $model->getAttributeLabel('rating'),
                    'value' => $model->rating,
                    'visible' => $model->type == User::TENANT
                ],
                'createdAt',
                'timeZone',
                [
                    'label' => $model->getAttributeLabel('companyRegNum'),
                    'value' => $model->companyRegNum,
                    'visible' => $model->type == User::OWNER
                ],
                [
                    'label' => $model->getAttributeLabel('companyName'),
                    'value' => $model->companyName,
                    'visible' => $model->type == User::OWNER
                ],
                [
                    'label' => $model->getAttributeLabel('companyType'),
                    'value' => $companyTypes[$model->companyType],
                    'visible' => $model->type == User::OWNER
                ],
            ],
        ])
        ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-default-custom panel-heading">
        <h3 class="panel-title"><?= Yii::t('app', 'General Bank Details') ?></h3>
    </div>
    <div class="panel-body">
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'bankName',
                'iban',
                'swift',
                'bankAccountName',
            ],
        ])
        ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-default-custom panel-heading">
        <h3 class="panel-title"><?= Yii::t('app', 'MangoPay Related Bank Details') ?></h3>
    </div>
    <div class="panel-body">
        <?=
        DetailView::widget([
            'model' => $userMpInfo,
            'attributes' => [
                'iban',
                'address',
                [
                    'label' => $model->getAttributeLabel('nationality'),
                    'value' => isset($nationalities[$userMpInfo->nationality]) ? $nationalities[$userMpInfo->nationality] : '-',
                ],
                [
                    'label' => $model->getAttributeLabel('countryOfResidence'),
                    'value' => isset($countries[$userMpInfo->countryOfResidence]) ? $countries[$userMpInfo->countryOfResidence] : '-',
                ],
                [
                    'label' => $model->getAttributeLabel('incomeRange'),
                    'value' => isset($incomeRanges[$userMpInfo->incomeRange]) ? $incomeRanges[$userMpInfo->incomeRange] : '-',
                ],
                'mpUserId',
                'occupation',
                'createdAt'
            ],
        ])
        ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-default-custom panel-heading">
        <h3 class="panel-title"><?= Yii::t('app', 'MangoPay Proof Documents') ?></h3>
    </div>
    <div class="panel-body">
        <?=
        GridView::widget([
            'id' => 'dataGrid',
            'dataProvider' => $mpFileProvider,
            'summary' => '',
            'tableOptions' => ['class'=>'table table-striped'],
            'columns' => [
                [
                    'attribute' => 'fileName',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->fileName, $model->getFileUrl(), ['target' => '_blank']);
                    }
                ],
                [
                    'attribute' => 'type',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->fileTypes[$model->type];
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->fileStatuses[$model->status];
                    }
                ],
                'mpDocId'
            ],
        ]);
        ?>
    </div>
</div>

<?php if($model->type == User::TENANT): ?>
<div class="panel panel-default">
    <div class="panel-default-custom panel-heading">
        <h3 class="panel-title"><?= Yii::t('app', 'Proof Documents') ?></h3>
    </div>
    <div class="panel-body">
        <?=
        GridView::widget([
            'id' => 'dataGrid',
            'dataProvider' => $provider,
            'summary' => '',
            'tableOptions' => ['class'=>'table table-striped'],
            'columns' => [
                [
                    'attribute' => 'fileName',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return Html::a($data['fileName'], $data['fileUrl'], ['target' => '_blank']);
                    }
                ],
                'type',
                'comment',
            ],
        ]);
        ?>
    </div>
</div>
<?php endif; ?>