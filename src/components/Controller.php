<?php
namespace app\components;

use yii\filters\AccessControl;

class Controller extends \yii\web\Controller
{
    public $layout = '@app/views/layouts/default';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ]
        ];
    }
}
