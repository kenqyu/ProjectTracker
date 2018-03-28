<?php
namespace app\models\enums;

use skinka\php\TypeEnum\BaseEnum;

class UserStatus extends BaseEnum
{
    const ACTIVE = 1;
    const DISABLED = 0;

    public static function getData()
    {
        return [
            self::ACTIVE => [
                'text' => 'Active'
            ],
            self::DISABLED => [
                'text' => 'Disabled'
            ]
        ];
    }
}
