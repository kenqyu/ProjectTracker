<?php
namespace app\models\enums;

use skinka\php\TypeEnum\BaseEnum;

/**
 * Class JobStatus
 * @package app\models\enums
 *
 * @method text() string
 * @method order() integer
 * @method label_color() string
 */
class JobStatus extends BaseEnum
{
    const NEW = 1;
    const MANAGER_REVIEW = 2;
    const MANAGER_APPROVED = 7;
    const IN_PROGRESS = 3;
    const COMPLETED = 4;
    const ON_HOLD = 5;
    const CANCELED = 6;
    const TRANSLATION_IN_PROGRESS = 8;

    public static function getData()
    {
        return [
            static::NEW => ['text' => 'New', 'order' => 1, 'label_color' => '#4D863D'],
            static::MANAGER_REVIEW => ['text' => 'Management Review', 'order' => 2, 'label_color' => '#4D863D'],
            static::MANAGER_APPROVED => ['text' => 'Management Approved', 'order' => 3, 'label_color' => '#4D863D'],
            static::IN_PROGRESS => ['text' => 'In Progress', 'order' => 4, 'label_color' => '#4D863D'],
            static::TRANSLATION_IN_PROGRESS => ['text' => 'Translation in Process', 'order' => 5, 'label_color' => '#4D863D'],
            static::COMPLETED => ['text' => 'Completed', 'order' => 7, 'label_color' => '#4D863D'],
            static::ON_HOLD => ['text' => 'On Hold', 'order' => 6, 'label_color' => '#4D863D'],
            static::CANCELED => ['text' => 'Cancelled', 'order' => 8, 'label_color' => '#4D863D']
        ];
    }

    public function getLabel()
    {
        return '<span class="label" style="background: ' . $this->label_color() . '">' . $this->text() . '</span>';
    }
}
