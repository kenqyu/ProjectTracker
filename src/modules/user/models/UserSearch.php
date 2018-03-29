<?php

namespace app\modules\user\models;

use app\models\enums\UserRoles;
use app\models\enums\UserStatus;
use app\models\OrganizationUnit;
use app\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends Model
{
    public $id;
    public $username;
    public $first_name;
    public $last_name;
    public $email;
    public $role;
    public $status;
    public $approved;
    public $organization_unit;

    public function rules()
    {
        return [
            [['id', 'organization_unit'], 'integer'],
            [['username', 'first_name', 'last_name', 'email'], 'string'],
            [['role'], 'in', 'range' => UserRoles::getKeys()],
            [['status'], 'in', 'range' => UserStatus::getKeys()],
            [['approved'], 'boolean']
        ];
    }

    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->organization_unit == -1) {
            $query->andWhere(['organization_unit_id' => null]);
        } else {
            $query->andFilterWhere(['organization_unit_id' => $this->organization_unit]);
        }

        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['role' => $this->role])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['approved' => $this->approved])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
