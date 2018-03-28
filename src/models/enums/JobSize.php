<?php
namespace app\models\enums;

use skinka\php\TypeEnum\BaseEnum;

class JobSize extends BaseEnum
{
    const SMALL = 1;
    //const MEDIUM = 2;
    const LARGE = 3;

    public static function getData()
    {
        return [
            static::SMALL => ['text' => 'Small'],
            //static::MEDIUM => ['text' => 'Medium'],
            static::LARGE => ['text' => 'Large']
        ];
    }
}
