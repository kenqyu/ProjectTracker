<?php
/**
 * @var $this \yii\web\View
 * @var $model array
 * @var $data array
 */

use Ramsey\Uuid\Uuid;

$id = Uuid::uuid4()->toString();

$out = [];
foreach ($data as $key => $item) {
    $out[] = [
        'name' => empty($key) ? '[empty]' : $key,
        'y' => $item,
        'drilldown' => empty($key) ? '[empty]' : $key,
    ];
}

?>
<?php if (empty($data)) {
    echo '<h3>No data found</h3>';
} else {
    echo \app\modules\reports\widgets\PieChartWidget::widget([
        'data' => $data,
        'width' => 1350 * ($width / 100)
    ]);
}