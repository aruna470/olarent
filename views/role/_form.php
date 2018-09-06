<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Role */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="role-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'permissionForm',
        'enableClientValidation' => false,
    ]);
    ?>

    <div class="row">
        <div class="col-md-3">
            <?php
            if ($model->isNewRecord) {
                echo $form->field($model, 'name')->textInput(['maxlength' => true]);
            } else {
                echo $form->field($model, 'name')->textInput(['readonly' => true]);
            }
            ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'description')->textInput([]) ?>
        </div>
    </div>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped'],
        'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
                'name' => 'Permission',
				'checkboxOptions' => function($data, $key, $index, $column) use ($model) {
					$checked = false;
                    if (in_array($data->name, $model->userPermissions) || (!$model->hasErrors() && in_array($data->name, $model->modelPermissions))) {
							$checked = true;
						}
					return ['value' => $data->name, 'checked' => $checked];
				}
			],
            'name',
            'description',
            'category',
        ],
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-info']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
