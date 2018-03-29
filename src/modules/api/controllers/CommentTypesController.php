<?php
namespace app\modules\api\controllers;

use app\components\ActiveApiController;
use app\models\JobCommentType;

class CommentTypesController extends ActiveApiController
{
    public $modelClass = JobCommentType::class;
}
