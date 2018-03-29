<?php
namespace app\modules\api\controllers;

use yii\web\Controller;

class ErrorController extends Controller
{
    public $layout = '@app/views/layouts/simple';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ]
        ];
    }
}
