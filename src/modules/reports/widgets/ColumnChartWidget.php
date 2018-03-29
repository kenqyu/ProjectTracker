<?php

namespace app\modules\reports\widgets;

use yii\base\Widget;
use CpChart\Chart\Pie;
use CpChart\Data;
use CpChart\Image;
use yii\helpers\Html;

class ColumnChartWidget extends Widget
{
    public $data = [];
    public $colors = [
        [
            'R' => 124,
            'G' => 181,
            'B' => 236,
            'Alpha' => 100,
        ],
        [
            'R' => 67,
            'G' => 67,
            'B' => 72,
            'Alpha' => 100,
        ],
        [
            'R' => 144,
            'G' => 237,
            'B' => 125,
            'Alpha' => 100,
        ],
        [
            'R' => 247,
            'G' => 163,
            'B' => 92,
            'Alpha' => 100,
        ],
        [
            'R' => 128,
            'G' => 133,
            'B' => 233,
            'Alpha' => 100,
        ],
        [
            'R' => 241,
            'G' => 92,
            'B' => 128,
            'Alpha' => 100,
        ],
        [
            'R' => 228,
            'G' => 211,
            'B' => 84,
            'Alpha' => 100,
        ],
        [
            'R' => 43,
            'G' => 144,
            'B' => 143,
            'Alpha' => 100,
        ],
        [
            'R' => 244,
            'G' => 91,
            'B' => 91,
            'Alpha' => 100,
        ],
        [
            'R' => 145,
            'G' => 232,
            'B' => 225,
            'Alpha' => 100,
        ]
    ];
    public $width = 300;
    public $unit = '';

    public function init()
    {

        $out = [];

        $sum = array_sum($this->data);

        $data = collect($this->data)->map(function ($item) use ($sum) {
            if ($sum == 0) {
                return 0;
            }
            return intval($item / ($sum / 100));
        })->all();


        foreach ($data as $key => $item) {
            $out[] = [
                'name' => empty($key) ? '[empty]' : $key,
                'y' => floatval($item)
            ];
        }


        $request = [
            'infile' => [
                "chart" => [
                    "height" => 300,
                    "width" => $this->width,
                    "type" => "column"
                ],
                "title" => ["text" => false],
                "credits" => ["enabled" => false],
                "yAxis" => [
                    "min" => 0,
                    "max" => 100,
                    "title" => ["text" => null],
                    "labels" => [
                        "format" => "{value}" . $this->unit,
                        "style" => [
                            "color" => "contrast",
                            "fontSize" => "14px",
                            //"fontWeight" => "bold",
                            "textOutline" => "1px contrast"
                        ]
                    ]
                ],
                "xAxis" => [
                    "type" => "category",
                    "labels" => [
                        "style" => [
                            "color" => "contrast",
                            "fontSize" => "14px",
                            "fontWeight" => "normal",
                            "textOutline" => "1px contrast"
                        ]
                    ]
                ],
                "legend" => ["enabled" => false],
                "series" => [
                    [
                        "data" => $out
                    ]
                ],
                "plotOptions" => [
                    "column" => [
                        "dataLabels" => [
                            "enabled" => true,
                            "format" => "{y}" . $this->unit,
                        ],
                    ],
                    "series" => [
                        "dataLabels" => [
                            "style" => [
                                "color" => "contrast",
                                "fontSize" => "14px",
                                "fontWeight" => "normal",
                                "textOutline" => "1px contrast"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $client = new \GuzzleHttp\Client();

        $response = $client->post(getenv('GRAPHS_URL'), [
            'json' => $request
        ]);

        echo Html::img('data:image/png;base64,' . base64_encode($response->getBody()));
    }
}
