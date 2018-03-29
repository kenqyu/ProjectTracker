<?php
namespace app\modules\api\controllers;

use app\components\ActiveApiController;
use app\models\Agency;

class AgencyController extends ActiveApiController
{
    public $modelClass = Agency::class;
}
