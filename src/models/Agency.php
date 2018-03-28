<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "agency".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Job[] $jobs
 */
class Agency extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agency';
    }

    public static function getDataList($withEmpty = false)
    {
        $out = ArrayHelper::map(static::find()->all(), 'id', 'name');
        if (!$withEmpty) {
            return $out;
        }

        return ['' => 'None'] + $out;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobs()
    {
        return $this->hasMany(Job::class, ['agency_id' => 'id']);
    }
}