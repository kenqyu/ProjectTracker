<?php
namespace app\models\enums;

use skinka\php\TypeEnum\BaseEnum;

class UserTypes extends BaseEnum
{
    const PROJECT_LEAD = 1;
    const PROJECT_MANAGER = 2;
    const IWCM_PUBLISHING_ASSIGNEE = 3;
    const CCC_CONTACT = 4;
    const SCE_APPROVER = 5;
    const TRANSLATION_MANAGER = 6;

    public static function getData()
    {
        return [
            static::PROJECT_LEAD => ['text' => 'Project Lead'],
            static::PROJECT_MANAGER => ['text' => 'Project Manager'],
            static::IWCM_PUBLISHING_ASSIGNEE => ['text' => 'CMS Assignee'],
            static::CCC_CONTACT => ['text' => 'CCC Contact'],
            static::SCE_APPROVER => ['text' => 'SCE Approver'],
            static::TRANSLATION_MANAGER => ['text' => 'Translation Manager'],
        ];
    }
}
