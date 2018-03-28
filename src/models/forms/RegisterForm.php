<?php

namespace app\models\forms;

use app\models\enums\UserRoles;
use app\models\User;
use Cocur\Slugify\Slugify;
use yii\base\Model;
use yii\web\JsExpression;

class RegisterForm extends Model
{
    public $first_name;
    public $last_name;
    public $username;
    public $phone;
    public $email;
    public $password;
    public $password_repeat;

    public $organization_unit;
    public $organization_unit_other;

    public $department_id;
    public $sub_department_id;

    public function rules()
    {
        return [
            [
                [
                    'username',
                    'password',
                    'password_repeat',
                    'email',
                    'first_name',
                    'last_name',
                    'phone',
                    'organization_unit'
                ],
                'required'
            ],
            [
                [
                    'username',
                    'password',
                    'password_repeat',
                    'first_name',
                    'last_name',
                    'organization_unit_other',
                    'phone'
                ],
                'string',
                'max' => 255
            ],
            [
                ['organization_unit_other'],
                'required',
                'when' => function ($model) {
                    return $model->organization_unit <= 0;
                },
                'whenClient' => new JsExpression("
                function (attribute, value) {
                    return $('#registerform-organization_unit').val() <= 0;
                }")
            ],
            [['organization_unit', 'department_id', 'sub_department_id'], 'integer'],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password'],
            [['email'], 'email'],
            [
                ['username'],
                'match',
                'pattern' => '/^[a-zA-Z0-9_\.]+$/',
                'message' => 'Username can contains lower- and upper-case letters and underscore'
            ],
            [['username'], 'unique', 'targetClass' => User::class],
            [['email'], 'unique', 'targetClass' => User::class]
        ];
    }

    public function attributeLabels()
    {
        return [
            'department_id' => 'Department',
            'sub_department_id' => 'Sub-Department',
            'organization_unit' => 'Organization Unit (OU)',
            'organization_unit_other' => 'Please Specify'
        ];
    }

    public function beforeValidate()
    {
        if (empty($this->username)) {
            $i = 0;
            do {
                $this->username = mb_strtolower((new Slugify())->slugify($this->first_name,
                        '_')) . '.' . mb_strtolower((new Slugify())->slugify($this->last_name,
                        '_')) . ($i == 0 ? '' : $i);
                $i++;
            } while (User::find()->where(['username' => $this->username])->exists());
        }

        return parent::beforeValidate();
    }

    public function register($role = UserRoles::GENERAL)
    {

        if ($this->validate()) {
            $model = new User();
            $model->first_name = $this->first_name;
            $model->last_name = $this->last_name;
            $model->username = $this->username;
            $model->email = $this->email;
            $model->setPassword($this->password);
            $model->role = $role;
            $model->phone = $this->phone;
            $model->organization_unit_id = $this->organization_unit;
            $model->organization_unit_other = $this->organization_unit_other;
            $model->department_id = $this->department_id;
            $model->sub_department_id = $this->sub_department_id;
            if ($model->save()) {
                if (!YII_CONSOLE) {
                    $message = \Yii::$app->mailer->compose('register_complete', ['model' => $this]);
                    $message->setFrom('no-reply@scemanagement.com');
                    $message->setTo($this->email);
                    $message->setSubject('(External):CX Project Tracker Welcome to SCE Project Tracker');
                    \Yii::$app->mailer->send($message);

                    foreach (User::find()->where(['role' => UserRoles::ADMIN, 'no_mails' => false])->all() as $admin) {

                        $message = \Yii::$app->mailer->compose('new_account_admin', ['model' => $admin]);
                        $message->setFrom('no-reply@scemanagement.com');

                        $message->setTo($admin->email);
                        $message->setSubject('(External):CX Project Tracker New User Registered');
                        \Yii::$app->mailer->send($message);
                    }

                }
                return true;
            }
        }
        return false;
    }
}
