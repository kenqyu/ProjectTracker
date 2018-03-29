<?php
namespace app\modules\api\controllers;

use app\components\ActiveApiController;
use app\models\WorkType;

class WorkTypeController extends ActiveApiController
{
    public $modelClass = WorkType::class;
}
