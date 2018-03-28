<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "job_custom_fields".
 *
 * @property integer $id
 * @property integer $job_id
 * @property string $label
 * @property integer $type
 * @property string $value
 * @property string $options
 */
class JobCustomFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_custom_fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['job_id', 'label', 'type'], 'required'],
            [['job_id', 'type'], 'integer'],
            [['options'], 'string'],
            [['label'], 'string', 'max' => 255],
            [['value'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'job_id' => 'Job ID',
            'label' => 'Label',
            'type' => 'Type',
            'value' => 'Value',
            'options' => 'Options',
        ];
    }
}