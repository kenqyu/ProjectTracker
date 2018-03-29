<?php

namespace app\modules\reports\models\enums;

use skinka\php\TypeEnum\BaseEnum;

class FieldType extends BaseEnum
{
    const HARDCODED = 0;
    const CUSTOM = 1;

    public static function getData()
    {
        return [
            self::HARDCODED => [
                'text' => 'Native Field'
            ],
            self::CUSTOM => [
                'text' => 'Custom Field'
            ]
        ];
    }
}
