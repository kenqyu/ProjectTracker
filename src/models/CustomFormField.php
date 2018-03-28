<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "custom_form_field".
 *
 * @property integer $id
 * @property integer $form_id
 * @property string $label
 * @property string $hint
 * @property integer $type
 * @property integer $required
 * @property string $options
 * @property string $default
 * @property integer $sort
 *
 * @property CustomForm $form
 */
class CustomFormField extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_form_field';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form_id', 'label', 'type'], 'required'],
            [['form_id', 'type', 'required', 'sort'], 'integer'],
            [['options', 'hint'], 'string'],
            [['label', 'default'], 'string', 'max' => 255],
            [
                ['form_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => CustomForm::class,
                'targetAttribute' => ['form_id' => 'id']
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
            'form_id' => 'Form ID',
            'label' => 'Label',
            'hint' => 'Hint',
            'type' => 'Type',
            'required' => 'Required',
            'options' => 'Options',
            'default' => 'Default value',
            'sort' => 'Sort'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(CustomForm::class, ['id' => 'form_id']);
    }

    public function getOptions()
    {
        return json_decode($this->options, true);
    }
}