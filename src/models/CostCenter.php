<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cost_center".
 *
 * @property integer $id
 * @property string $name
 *
 * @property JobCostCenter[] $jobCostCenters
 */
class CostCenter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cost_center';
    }

    public static function getDataList()
    {
        return ArrayHelper::map(static::find()->all(), 'id', 'name');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
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
    public function getJobCostCenters()
    {
        return $this->hasMany(JobCostCenter::class, ['cost_center_id' => 'id']);
    }
}