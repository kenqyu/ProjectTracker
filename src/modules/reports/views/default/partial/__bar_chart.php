<?php
/**
 * @var $this \yii\web\View
 * @var $model array
 * @var $data array
 * @var $isPercent boolean
 */

use Ramsey\Uuid\Uuid;

$id = Uuid::uuid4()->toString();

$out = [];

$sum = array_sum($data);

$data = collect($data)->map(function ($item) use ($sum) {
    if ($sum == 0) {
        return 0;
    }
    return intval($item / ($sum / 100));
})->all();

$isPercent = true;

foreach ($data as $key => $item) {
    $out[] = [
        'name' => empty($key) ? '[empty]' : $key,
        'y' => floatval($item)
    ];
}

$preparedData = json_encode($out);
$postfix = '';
$max = '';
if (isset($isPercent) && $isPercent) {
    $postfix = '%';
    $max = 'max: 100,';
}
?>

<?php if (empty($data)) {
    echo '<h3>No data found</h3>';
} else { ?>
    <div class="chart" id="chart_<?= $id ?>"></div>
    <?php $this->registerJs(<<<JS
Highcharts.chart($('#chart_{$id}').get(0), {
    chart: {
        height: 300,
        type: 'column'
    },
    title: {
        text: false
    },
    credits: {
        enabled: false
    },
    yAxis: {
        min: 0,
        {$max}
        title: {
            text: null
        },
        labels: {
            format: '{value}{$postfix}'
        }
    },
    xAxis: {
        type: 'category',
        style : {
                    "color": "contrast",
                    "fontSize": "14px",
                    "fontWeight": "normal",
                    "textOutline": "1px contrast"
                }
    },
    legend: {
        enabled: false,
         labelFormatter: function(){
            return names[this.index-1];
        }
    },

    series: [{
        tooltip: {
            pointFormat: '<span style="color:{point.color}">\u25CF</span> <strong>{point.y}{$postfix}</strong><br/>'
        },
        data: {$preparedData}
    }],

    plotOptions: {
         column: {
            dataLabels: {
                enabled: true,
                format: '{y}{$postfix}',
                style : {
                    "color": "contrast",
                    "fontSize": "14px",
                    "fontWeight": "normal",
                    "textOutline": "1px contrast"
                }
            },
        },
        pie: {
            size: '200px',
            showInLegend: true,
            colors: ['#7cb5ec', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#95959d', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1']
        }
    },
});
JS
    );
}
?>