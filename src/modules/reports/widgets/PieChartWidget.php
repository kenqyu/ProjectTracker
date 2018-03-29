<?php

namespace app\modules\reports\widgets;

use yii\base\Widget;
use CpChart\Chart\Pie;
use CpChart\Data;
use CpChart\Image;
use yii\helpers\Html;

class PieChartWidget extends Widget
{
    public $data = [];
    public $colors = [
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
    public $width;

    public function init()
    {
        $out = [];
        foreach ($this->data as $key => $item) {
            $out[] = [
                'name' => empty($key) ? '[empty]' : $key,
                'y' => $item,
                'drilldown' => empty($key) ? '[empty]' : $key,
            ];
        }

        $preparedData = $out;

        $request = [
            'infile' => [
                "chart" => [
                    "height" => 300,
                    "width" => $this->width,
                    "type" => "pie"
                ],
                "title" => ["text" => false],
                "credits" => ["enabled" => false],
                "series" => [
                    [
                        "data" => $preparedData
                    ]
                ],
                "plotOptions" => [
                    "pie" => [
                        "size" => "200px",
                        "colors" => $this->colors
                    ],
                    "series" => [
                        "dataLabels" => [
                            "enabled" => true,
                            "format" => "{point.percentage:.0f} %",
                            "distance" => 15,
                            "style" => [
                                "color" => "contrast",
                                "fontSize" => "14px",
                                "fontWeight" => "normal",
                                "textOutline" => "1px contrast"
                            ]
                        ]
                    ]
                ]
            ],
            //'scale' => 2
        ];

        $client = new \GuzzleHttp\Client();

        $response = $client->post(getenv('GRAPHS_URL'), [
            'json' => $request
        ]);

        while (count($this->data) > count($this->colors)) {
            $this->colors = array_merge($this->colors, $this->colors);
        }

        echo Html::img('data:image/png;base64,' . base64_encode($response->getBody()));

        $i = 0;
        $legend = [];
        foreach ($this->data as $key => $item) {
            $legend[] = '<span class="item" style="font-size: 15px"><span style="color: ' . $this->colors[$i] . '">●</span> ' . $key . '</span>';
            $i++;
        }
        echo Html::tag('div', implode('', $legend), ['class' => 'legend']);
    }
}
