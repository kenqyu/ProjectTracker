<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Report
 * @var $processingUnit \app\models\ProcessingUnit
 * @var $dates \DateTime[]
 * @var $totalJobs integer
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: "Lucida Grande", "Lucida Sans Unicode", Arial, Helvetica, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        img {
            max-width: 100%;
        }

        .header {
            margin-bottom: 30px;
        }

        .header .logo {
            width: 20%;
        }

        .header .info {
            text-align: center;
        }

        .header .info h1 {
            margin: 0;
        }

        .header .info h3 {
            margin: 0;
            font-weight: normal;
        }

        .report_row.first_page_item {
            margin-bottom: 40px;
        }

        .legend {
            list-style: none;
            padding: 0;
            margin: 10px 0;
            display: block;
        }

        .legend .item {
            /*display: inline-block;*/
            padding: 0;
            margin: 0 10px 0 0;
            font-size: 14px;
            white-space: nowrap;
        }

        .legend .item span {
            font-family: DejaVu Sans;
            font-size: 10px;
            white-space: nowrap;
            position: relative;
            vertical-align: middle;
        }

        footer {
            position: fixed;
            bottom: 80px;
            left: 0;
            right: 0;
            width: 100%;
            display: inline-block;
            white-space: nowrap;
        }

        footer .pagenum-container {
            display: inline-block;
            text-align: right;
            width: 20%;
            vertical-align: bottom;
        }

        footer .footnote {
            display: inline-block;
            width: 80%;
            white-space: normal;
            vertical-align: middle;
        }

        footer .footnote p {
            margin: 0;
        }

        footer .pagenum:before {
            content: counter(page);
        }

        h3 {
            font-size: 24px;
        }
    </style>
</head>
<body>
<footer>
    <div style="text-align: center;">Internal Use Only</div>
    <div class="footnote">
        <?= $model->footnote ?>
    </div>
    <div class="pagenum-container">Page <span class="pagenum"></span></div>
    <div style="clear: both;"></div>
</footer>
<div class="header">
    <div class="logo">
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJUAAAA1CAYAAABIvqmhAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDoxNzU1RUI2MDE4MjA2ODExODcxRkZFOTY3RjJDM0ZCOSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGQkM5MzZDRUM4NTExMUUxQUI4RUQ0MjhFNTBBN0QxMiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGQkM5MzZDREM4NTExMUUxQUI4RUQ0MjhFNTBBN0QxMiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IE1hY2ludG9zaCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRGODAyQzJGNjkyMDY4MTE4QTZEOTFBNjc4QjY4NUZFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjE3NTVFQjYwMTgyMDY4MTE4NzFGRkU5NjdGMkMzRkI5Ii8+IDwvcmRmOkRlc2NyaXB0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">
    </div>
    <div class="info">
        <h1><?= $model->name ?></h1>
        <h3><?= \app\models\ProcessingUnit::findOne($model->processing_unit_id)->name ?? '' ?></h3>
        <h3><?= $dates[0]->format('m/d/Y') . ' - ' . $dates[1]->format('m/d/Y') ?></h3>
        <h3 class="total_jobs">
            Total Requests - <?= $totalJobs ?>
        </h3>
        <h3 class="total_jobs">
            Total <span style="text-decoration: underline;">Completed</span> Requests - <?= $totalCompletedJobs ?>
        </h3>
    </div>
    <div style="clear: both;"></div>
</div>
<div class="rows">
    <?php
    $data = $model->getDecodedContent();
    $c = 1;
    $i = 1;
    $page = 1;
    if (isset($data['rows'])) {
        foreach ($data['rows'] as $item) {
            echo $this->render('partial/_row',
                ['rootModel' => $model, 'model' => $item, 'dates' => $dates, 'page' => $page]);
            if ((($page == 1 && $i == 3) || ($page != 1 && $i == 4)) && $c != count($data['rows'])) {
                echo '<div style="page-break-after: always;"></div>';
                $i = 0;
                $page++;
            }
            $i++;
            $c++;
        }
    }
    ?>
</div>
</body>
