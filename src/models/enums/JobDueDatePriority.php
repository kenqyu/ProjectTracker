<?php
namespace app\models\enums;

use skinka\php\TypeEnum\BaseEnum;

class JobDueDatePriority extends BaseEnum
{
    const STANDARD = 1;
    const RUSH = 2;
    const MANDATED = 3;

    public static function getData()
    {
        return [
            static::STANDARD => ['text' => 'Standard'],
            static::RUSH => ['text' => 'Rush'],
            static::MANDATED => ['text' => 'Mandated']
        ];
    }
}
