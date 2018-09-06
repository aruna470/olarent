<?php

use yii\web\View;
use app\assets\GoogleChartAsset;

GoogleChartAsset::register($this);
?>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-default-custom panel-heading">
                <h3 class="panel-title"><?= Yii::t('app', 'User(Owner/Tenant) registration of last 7 days') ?></h3>
            </div>
            <div class="panel-body">
                <div id="regChart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-default-custom panel-heading">
                <h3 class="panel-title"><?= Yii::t('app', 'Tenant payments of last 7 days') ?></h3>
            </div>
            <div class="panel-body">
                <div id="paymentChart"></div>
            </div>
        </div>
    </div>
</div>

<?php
$regChartLblX = Yii::t('app', 'Date');
$regChartLblY = Yii::t('app', 'Count');

$paymentChartLblX = Yii::t('app', 'Date');
$paymentChartLblY = Yii::t('app', 'Total({currency})', ['currency' => Yii::$app->params['defCurrency']]);

$script = <<< JS
    //google.load('visualization', '1', {packages: ['corechart', 'bar']});
    //google.setOnLoadCallback(drawRegChart);
    //google.setOnLoadCallback(drawPaymentChart);

    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawRegChart);
    google.charts.setOnLoadCallback(drawPaymentChart);


    function drawRegChart() {
        var data = google.visualization.arrayToDataTable({$regCounts});
        var options = {
            colors: ['#E0655B'],
            annotations: {
                alwaysOutside: true,
                textStyle: {
                    fontSize: 12,
                    color: '#000',
                    auraColor: 'none'
                }
            },
            hAxis: {
                title: '{$regChartLblX}',
                direction: -1,
                slantedText: true,
                slantedTextAngle: 45,
                textStyle: {
                    fontSize: '12',
                    paddingLeft: '0',
                    marginRight: '0'
                }
            },
            vAxis: {
                title: '{$regChartLblY}'
            },
            width: '100%',
            height: 325,
            legend : 'none',
            chartArea: {
                left:60,
                top:20,
                bottom:100,
                width:"100%"
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('regChart'));
        chart.draw(data, options);
    }


    function drawPaymentChart() {
        var data = google.visualization.arrayToDataTable({$totPayments});
        var options = {
            colors: ['#588E0C'],
            annotations: {
                alwaysOutside: true,
                textStyle: {
                    fontSize: 12,
                    color: '#000',
                    auraColor: 'none'
                }
            },
            hAxis: {
                title: '{$paymentChartLblX}',
                direction: -1,
                slantedText: true,
                slantedTextAngle: 45,
                textStyle: {
                    fontSize: '12',
                    paddingLeft: '0',
                    marginRight: '0'
                }
            },
            vAxis: {
                title: '{$paymentChartLblY}'
            },
            width: '100%',
            height: 325,
            legend : 'none',
            chartArea: {
                left:60,
                top:20,
                bottom:100,
                width:"100%"
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('paymentChart'));
        chart.draw(data, options);
    }
JS;

$this->registerJs($script, View::POS_END);
?>


