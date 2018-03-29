<?php
/**
 * @var $this \yii\web\View
 * @var $isPercent boolean
 * @var $data array
 * @var $width float
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
        'y' => $item
    ];
}

$preparedData = json_encode($out);

?>

<?php if (empty($data)) {
    echo '<h3>No data found</h3>';
} else {
    echo \app\modules\reports\widgets\ColumnChartWidget::widget([
        'data' => $data,
        'width' => 1350 * ($width / 100),
        'unit' => $isPercent ? '%' : ''
    ]);
}