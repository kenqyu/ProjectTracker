<?php

namespace app\models\queries;

use app\models\enums\UserStatus;

/**
 * This is the ActiveQuery class for [[\app\models\User]].
 *
 * @see \app\models\User
 */
class UserQuery extends \yii\db\ActiveQuery
{
    public function needActivation()
    {
        return $this->andWhere(['approved' => false]);
    }

    /**
     * @inheritdoc
     * @return \app\models\User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function activated()
    {
        return $this->andWhere(['approved' => true]);
    }
}