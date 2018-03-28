<?php

namespace app\models\queries;

use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[\app\models\Notifications]].
 *
 * @see \app\models\Notifications
 */
class NotificationsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\models\Notifications[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Notifications
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function currentUser()
    {
        return $this->andWhere(['user_id' => \Yii::$app->user->id]);
    }

    public function needToInformed()
    {
        return $this
            ->andWhere(['sent' => 0]);
    }
}