<?php

namespace app\models\queries;

use app\models\Job;

/**
 * This is the ActiveQuery class for [[\app\models\Job]].
 *
 * @see \app\models\Job
 */
class JobQuery extends \yii\db\ActiveQuery
{
    public function withAll()
    {
        $model = new Job();
        $relations = $model->getRelations();
        $relations = collect($relations)->map(function ($item) {
            return $item . ' ' . $item;
        });
        return $this->joinWith($relations->all());
    }

    /**
     * @inheritdoc
     * @return \app\models\Job[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Job|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}