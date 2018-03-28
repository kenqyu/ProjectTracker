<?php
namespace app\models\forms;

use app\models\User;
use yii\base\Model;

class UserForm extends Model
{
    /**
     * @var User
     */
    public $model;


    public $username;
    public $new_password;
    public $new_password_repeat;
    public $email;
    public $first_name;
    public $last_name;
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'first_name', 'last_name'], 'required'],
            [['new_password', 'new_password_repeat'], 'required', 'on' => 'create'],
            [
                [
                    'username',
                    'new_password',
                    'new_password_repeat',
                    'email',
                    'first_name',
                    'last_name'
                ],
                'string',
                'max' => 255
            ],
            [['new_password'], 'compare', 'compareAttribute' => 'new_password_repeat', 'skipOnEmpty' => true],
            [
                ['username'],
                'match',
                'pattern' => '/^[a-zA-Z0-9_]+$/',
                'message' => 'Username can contains lower- and upper-case letters and underscore'
            ],
            [['username'], 'unique', 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->model->id]],
            [['email'], 'unique', 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->model->id]]
        ];
    }

    public function save()
    {
        $this->model->setAttributes($this->getAttributes());
        if (!empty($this->new_password)) {
            $this->model->setPassword($this->new_password);
        }
        return $this->model->save();
    }
}
