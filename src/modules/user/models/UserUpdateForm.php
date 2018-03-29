<?php

namespace app\modules\user\models;

use app\models\Departments;
use app\models\enums\UserRoles;
use app\models\enums\UserTypes;
use app\models\ProcessingUnit;
use app\models\User;
use yii\base\Model;
use yii\bootstrap\Html;

class UserUpdateForm extends Model
{
    /**
     * @var User
     */
    public $model;

    public $id;
    public $username;
    public $email;
    public $phone;
    public $password;
    public $first_name;
    public $last_name;
    public $status;
    public $role;
    public $no_mails;

    public $organization_unit_id;
    public $organization_unit_other;
    public $department_id;
    public $sub_department_id;

    public $types = [];
    public $processing_units = [];

    public function rules()
    {
        return [
            [['username', 'email', 'first_name', 'last_name', 'role', 'phone'], 'required'],
            [['status', 'department_id', 'organization_unit_id', 'sub_department_id'], 'integer'],
            [['types', 'processing_units'], 'safe'],
            [['no_mails'], 'boolean'],
            [
                [
                    'username',
                    'password',
                    'email',
                    'first_name',
                    'last_name',
                    'organization_unit_other'
                ],
                'string',
                'max' => 255
            ],
            [['username'], 'unique', 'targetClass' => User::class, 'filter' => ['id' => 'id']],
            [['email'], 'unique', 'targetClass' => User::class, 'filter' => ['id' => 'id']],
            [['status'], 'default', 'value' => \app\models\enums\UserStatus::ACTIVE],
            [['status'], 'in', 'range' => \app\models\enums\UserStatus::getKeys()],
            [['role'], 'in', 'range' => \app\models\enums\UserRoles::getKeys()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'no_mails' => 'Disable non-subscription email',
            'organization_unit_id' => 'User Organizational Unit (OU)',
            'department_id' => 'User Department',
            'sub_department_id' => 'User Sub-Department',
            'organization_unit_other' => 'Please Specify'
        ];

    }

    public function attributeHints()
    {
        return [
            'password' => 'Leave this field empty if you don\'t want to change password.'
        ];
    }

    public function setModel(User $model)
    {
        $this->model = $model;
        $this->setAttributes($model->getAttributes(null, ['password']));
        $this->id = $model->id;
        foreach ($model->userTypes as $type) {
            $this->types[] = $type->type_id;
        }
        foreach ($model->processingUnits as $department) {
            $this->processing_units[] = $department->id;
        }
    }

    public function save()
    {
        if ($this->validate()) {
            $attributes = $this->getAttributes();
            unset($attributes['password']);
            $this->model->setAttributes($attributes);
            if (!empty($this->password)) {
                $this->model->setPassword($this->password);
            }
            if ($this->model->save()) {
                $this->saveTypes();
                $this->saveProcessingUnits();
                $this->model->save();
                return true;
            }
            $this->addErrors($this->model->getErrors());
            \Yii::$app->session->setFlash('error', Html::errorSummary($this));
        }
        return false;
    }

    protected function saveTypes()
    {
        $this->model->unlinkAll('userTypes', true);
        if (!empty($this->types)) {
            foreach ($this->types as $role) {
                if (UserTypes::getByValue($role)) {
                    $model = new \app\models\UserTypes();
                    $model->type_id = $role;
                    $this->model->link('userTypes', $model);
                }
            }
        }
    }

    protected function saveProcessingUnits()
    {
        $this->model->unlinkAll('processingUnits', true);
        if (!empty($this->processing_units)) {
            foreach ($this->processing_units as $processing_unit) {
                if ($model = ProcessingUnit::findOne($processing_unit)) {
                    $this->model->link('processingUnits', $model);
                }
            }
        }
    }
}
