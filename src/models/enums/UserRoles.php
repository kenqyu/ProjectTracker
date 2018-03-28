<?php
namespace app\models\enums;

use skinka\php\TypeEnum\BaseEnum;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

class UserRoles extends BaseEnum
{
    const GENERAL = 0;
    const MANAGER = 1;
    const REVIEWER = 2;
    const ADMIN = 3;

    public static function getData()
    {
        $out = [];

        $out[static::GENERAL] = [
            'text' => 'Requestor (general user)',
            'color' => 'success'
        ];
        $out[static::REVIEWER] = [
            'text' => 'Manager/Reviewer',
            'color' => 'warning'
        ];
        $out[static::MANAGER] = [
            'text' => 'Manager',
            'color' => 'warning'
        ];
        $out[static::ADMIN] = [
            'text' => 'Admin',
            'color' => 'danger'
        ];

        return $out;
    }

    public function label()
    {
        return Html::tag('span', $this->text(), ['class' => 'label label-' . $this->color()]);
    }
}
