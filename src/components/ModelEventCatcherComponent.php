<?php
/**
 * Created by Alex Bond at UPDG.
 * Date: 3/26/15 8:50 PM
 */

namespace app\components;


use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Model;
use yii\db\ActiveRecord;

class ModelEventCatcherComponent implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        Event::on(Model::class, Model::EVENT_AFTER_VALIDATE, function ($event) {
            /**
             * @var $model Model
             */
            $model = $event->sender;

            if ($model->hasErrors()) {
                \Yii::error("Validation error in " . $model->className() . ": " . json_encode($model->getErrors()));
            }
        });
        Event::on(ActiveRecord::class, ActiveRecord::EVENT_AFTER_VALIDATE, function ($event) {
            /**
             * @var $model Model
             */
            $model = $event->sender;

            if ($model->hasErrors()) {
                \Yii::error("Validation error in " . $model->className() . ": " . json_encode($model->getErrors()));
            }
        });
    }
}