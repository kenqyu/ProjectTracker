<?php

namespace app\models\enums;

use skinka\php\TypeEnum\BaseEnum;

class JobTranslationStatus extends BaseEnum
{
    const UPLOADED = 0;
    const SENT = 1;
    const RECEIVED = 2;
    const CANCELED = 3;
    const REQUESTED = 4;

    public static function getData()
    {
        return [
            self::SENT => ['text' => 'Translation sent'],
            self::RECEIVED => ['text' => 'Translation received'],
            self::UPLOADED => ['text' => 'Uploaded'],
            self::CANCELED => ['text' => 'Canceled'],
            self::REQUESTED => ['text' => 'Requested']
        ];
    }
}
