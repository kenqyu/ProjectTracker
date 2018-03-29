<?php

namespace app\modules\reports\models\enums;

use skinka\php\TypeEnum\BaseEnum;

/**
 * Class NativeField
 * @package app\modules\reports\models\enums
 *
 * @method text() string
 * @method field() string
 */
class NativeField extends BaseEnum
{
    const PROJECT_MANAGER = 1;
    const REQUEST_TYPE = 2;
    const STATUS = 3;
    const CREATED_BY = 4;
    const PROJECT_LEAD = 5;
    const PROCESSING_UNIT = 6;

    const CREATOR_ORGANIZATION_UNIT = 7;
    const CREATOR_SUB_DEPARTMENT = 8;
    const CREATOR_DEPARTMENT = 10;

    const COMPLETION_TIME_FRAME = 9;

    public static function getData()
    {
        return [
            self::PROJECT_MANAGER => [
                'text' => 'Project Manager',
                'field' => 'project_manager_id'
            ],
            self::REQUEST_TYPE => [
                'text' => 'Request Type',
                'field' => 'request_type_id'
            ],
            self::STATUS => [
                'text' => 'Status',
                'field' => 'job.status'
            ],
            self::CREATED_BY => [
                'text' => 'Job Creator',
                'field' => 'creator_id'
            ],
            self::PROJECT_LEAD => [
                'text' => 'Project Lead',
                'field' => 'project_lead_id'
            ],
            self::PROCESSING_UNIT => [
                'text' => 'Processing Unit',
                'field' => 'processing_unit_id'
            ],
            self::CREATOR_ORGANIZATION_UNIT => [
                'text' => 'Requestor\'s Organizational Unit',
                'field' => 'creator.organization_unit_id'
            ],
            self::CREATOR_DEPARTMENT => [
                'text' => 'Requestor\'s Department',
                'field' => 'creator.department_id'
            ],
            self::CREATOR_SUB_DEPARTMENT => [
                'text' => 'Requestor\'s Sub Department',
                'field' => 'creator.sub_department_id'
            ],
            self::COMPLETION_TIME_FRAME => [
                'text' => 'Time to complete',
                'field' => 'completion_time_frame'
            ]
        ];
    }
}
