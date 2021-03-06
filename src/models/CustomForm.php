<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "custom_form".
 *
 * @property integer $id
 * @property string $name
 *
 * @property CustomFormField[] $customFormFields
 */
class CustomForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_form';
    }

    public static function getDataList($withEmpty = false)
    {
        $r = static::find();
        $out = ArrayHelper::map($r->all(), 'id', 'name');
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
    public function getCustomFormFields()
    {
        return $this->hasMany(CustomFormField::class, ['form_id' => 'id']);
    }
}