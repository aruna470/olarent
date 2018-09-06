<?php
use yii\bootstrap\Tabs;
?>
<div style="margin-bottom:15px">
<?php
$curAction = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
$items = [
	[
		'label' => Yii::t('app', 'Matter Details'),
        'active' => $curAction == 'target/create',
		'url' => Yii::$app->urlManager->createUrl(['target/create']),
        'visible' => Yii::$app->user->can('Target.Create'),
	],
	[
		'label' => Yii::t('app', 'Report'),
		'active' => $curAction == 'client-response/create',
		'url' => Yii::$app->urlManager->createUrl(['client-response/create']),
		'visible' => Yii::$app->user->can('ClientResponse.Create')
	],
	[
		'label' => Yii::t('app', 'Images'),
        'active' => $curAction == 'case-file/create',
		'url' => Yii::$app->urlManager->createUrl(['case-file/create']),
		'visible' => Yii::$app->user->can('CaseFile.Create')
	],	
	[
		'label' => Yii::t('app', 'QC Search'),
        'active' => $curAction == 'qc-search/create',
		'url' => Yii::$app->urlManager->createUrl(['qc-search/create']),
		'visible' => Yii::$app->user->can('QcSearch.Create'),
	],
	[
		'label' => Yii::t('app', 'QC Briefing'),
        'active' => $curAction == 'qc-brief/create',
		'url' => Yii::$app->urlManager->createUrl(['qc-brief/create']),
		'visible' => Yii::$app->user->can('QcBrief.Create')
	],
	[
		'label' => Yii::t('app', 'Clinical Assessment'),
		'active' => $curAction == 'clinical-assessment/create',
		'url' => Yii::$app->urlManager->createUrl(['clinical-assessment/create']),
		'visible' => Yii::$app->user->can('ClinicalAssessment.Create')

	],
	[
		'label' => Yii::t('app', 'Financial Assessment'),
		'active' => $curAction == 'financial-assessment/create',
		'url' => Yii::$app->urlManager->createUrl(['financial-assessment/create']),
		'visible' => Yii::$app->user->can('FinancialAssessment.Create')
	],

	[
		'label' => Yii::t('app', 'Accounting'),
		'active' => $curAction == 'invoice/create',
		'url' => Yii::$app->urlManager->createUrl(['invoice/create']),
		'visible' => Yii::$app->user->can('Invoice.Create')
	],
];

$allowedItems = [];
foreach ($items as $item) {
	if ($item['visible']) {
		$allowedItems[] = $item;
	}
}

echo Tabs::widget([
    'items' => $allowedItems,
]);
?>
</div>
