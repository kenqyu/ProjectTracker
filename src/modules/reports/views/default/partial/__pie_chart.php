<?php
/**
 * @var $this \yii\web\View
 * @var $model array
 * @var $data array
 */

use Ramsey\Uuid\Uuid;
use yii\helpers\Html;

$id = Uuid::uuid4()->toString();

$out = [];
foreach ($data as $key => $item) {
    $out[] = [
        'name' => empty($key) ? '[empty]' : $key,
        'y' => $item,
        'drilldown' => empty($key) ? '[empty]' : $key,
    ];
}

$preparedData = json_encode($out);

?>
<?php if (empty($data)) {
    echo '<h3>No data found</h3>';
} else { ?>
    <div class="chart" id="chart_<?= $id ?>"></div>
    <?php $this->registerJs(<<<JS
Highcharts.chart($('#chart_{$id}').get(0), {
    chart: {
        height: 300,
        type: 'pie'
    },
    title: {
        text: false
    },
    credits: {
        enabled: false
    },
    legend: false,
    series: [{
        tooltip: {
            pointFormat: '<span style="color:{point.color}">\u25CF</span> <strong>{point.y}</strong><br/>'
        },
        data: {$preparedData},
    }],

    plotOptions: {
        pie: {
            size: '230px',
            showInLegend: true,
            colors: ['#7cb5ec', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#95959d', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1', '#86592d', '#339933', '#9900ff', '#ff0000', '#ff99ff', '#800000', '#d1d1e0']
        },
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.percentage:.0f} %',
                distance: 15,
                style : {
                    "color": "contrast",
                    "fontSize": "12px",
                    "fontWeight": "normal",
                    "textOutline": "1px contrast"
                }
            }
        }
    },
});
JS
    );
    $colors = [
        '#7cb5ec',
        '#90ed7d',
        '#f7a35c',
        '#8085e9',
        '#f15c80',
        '#95959d',
        '#e4d354',
        '#2b908f',
        '#f45b5b',
        '#91e8e1',
        '#86592d',
        '#339933',
        '#9900ff',
        '#ff0000',
        '#ff99ff',
        '#800000',
        '#d1d1e0'
    ];
    while (count($data) > count($colors)) {
        $colors = array_merge($colors, $colors);
    }
    $i = 0;
    $legend = [];
    foreach ($data as $key => $item) {
        $legend[] = '<span class="item" style="font-size: 14px;"><span style="color: ' . $colors[$i] . '">●</span> ' . $key . '</span>';
        $i++;
    }
    echo Html::tag('div', implode('', $legend), ['class' => 'legend']);
}
?>