<?php
namespace app\components;

use yii\base\Component;

class PusherComponent extends Component
{
    public $auth_key;
    public $secret;
    public $app_id;

    private $_pusher;

    /**
     * @return PusherComponent
     */
    public static function getInstance()
    {
        return \Yii::$app->pusher;
    }

    public function getPusher()
    {
        if (!$this->_pusher) {
            $this->_pusher = new \Pusher($this->auth_key, $this->secret, $this->app_id);
        }
        return $this->_pusher;
    }
}
