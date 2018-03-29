<?php
namespace app\modules\api\controllers;

use app\components\ActiveApiController;
use app\models\CostCenter;

class CostCenterController extends ActiveApiController
{
    public $modelClass = CostCenter::class;
}
