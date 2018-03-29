<?php
namespace app\modules\user\models;

use app\models\User;
use yii\base\Model;

/**
 * ForgotPassword form
 */
class ForgotPasswordForm extends Model
{
    public $email;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required', 'message' => 'Enter your email'],
            [['email'], 'email']
        ];
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if (!$this->_user) {
            $this->_user = User::find()->where(['email' => $this->email])->one();
        }

        return $this->_user;
    }
}
