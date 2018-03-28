<?php

namespace app\models\enums;

use skinka\php\TypeEnum\BaseEnum;

/**
 * Class CustomFormFieldType
 * @package app\models\enums
 *
 * @method text()
 * @method type()
 * @method code()
 */
class CustomFormFieldType extends BaseEnum
{
    const VARCHAR = 1;
    const DATE = 2;
    const DATETIME = 3;
    const SELECT = 4;
    const CHECKBOX = 5;
    const CHECKBOX_LIST = 6;

    public static function getData()
    {
        return [
            static::VARCHAR => ['text' => 'Regular input', 'type' => 'input', 'code' => 'varchar'],
            static::DATE => ['text' => 'Date', 'type' => 'input', 'code' => 'date'],
            static::DATETIME => ['text' => 'Date & time', 'type' => 'input', 'code' => 'datetime'],
            static::SELECT => ['text' => 'Select', 'type' => 'select', 'code' => 'select'],
            static::CHECKBOX => ['text' => 'Checkbox', 'type' => 'input', 'code' => 'checkbox'],
            static::CHECKBOX_LIST => ['text' => 'Checkbox List', 'type' => 'input', 'code' => 'checkbox_list']
        ];
    }
}
