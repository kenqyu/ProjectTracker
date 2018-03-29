<?php

namespace app\modules\reports\models\enums;

use skinka\php\TypeEnum\BaseEnum;

class SectionType extends BaseEnum
{
    const TEXTAREA = 0;
    const PIE_CHART = 1;
    const BAR_CHART = 2;

    public static function getData()
    {
        return [
            self::TEXTAREA => [
                'text' => 'Text area'
            ],
            self::PIE_CHART => [
                'text' => 'Pie Chart'
            ],
            self::BAR_CHART => [
                'text' => 'Bar Chart'
            ]
        ];
    }
}
