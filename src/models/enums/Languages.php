<?php
namespace app\models\enums;

use skinka\php\TypeEnum\BaseEnum;

class Languages extends BaseEnum
{
    const SPANISH = 1;
    const CHINESE = 2;
    const KOREAN = 3;
    const VIETNAMESE = 4;
    const OTHER = 5;

    public static function getData()
    {
        return [
            self::SPANISH => ['text' => 'Spanish'],
            self::CHINESE => ['text' => 'Chinese'],
            self::KOREAN => ['text' => 'Korean'],
            self::VIETNAMESE => ['text' => 'Vietnamese'],
            self::OTHER => ['text' => 'Other']
        ];
    }
}
