<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sub_department".
 *
 * @property integer $id
 * @property integer $department_id
 * @property string $name
 *
 * @property Departments $department
 */
class SubDepartment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sub_department';
    }

    public static function getDataListByDepartment($id)
    {
        return ArrayHelper::map(static::find()->where(['department_id' => $id])->all(), 'id', 'name');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['department_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [
                ['department_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Departments::className(),
                'targetAttribute' => ['department_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'department_id' => 'Department ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Departments::className(), ['id' => 'department_id']);
    }
}