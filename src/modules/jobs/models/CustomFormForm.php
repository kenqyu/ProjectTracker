<?php

namespace app\modules\jobs\models;

use app\models\CustomForm;
use app\models\CustomFormField;
use yii\base\Model;
use yii\db\ActiveQuery;

class CustomFormForm extends Model
{
    /**
     * @var CustomForm
     */
    public $model;

    public $id;
    public $name;

    /**
     * @var array[]
     */
    public $fields = [];

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [
                ['name'],
                'unique',
                'targetClass' => CustomForm::class,
                'filter' => function ($query) {
                    /** @var ActiveQuery $query */
                    return $this->model->isNewRecord ? $query : $query->andWhere(['!=', 'id', $this->model->id]);
                }
            ],
            [['fields'], 'safe'],
        ];
    }

    public function setModel(CustomForm $model)
    {
        $this->model = $model;
        $this->id = $model->id;
        $this->name = $model->name;
        foreach ($model->customFormFields as $field) {
            $this->fields[] = [
                'label' => $field->label,
                'type' => $field->type,
                'required' => $field->required,
                'options' => empty($field->options) ? [] : json_decode($field->options),
                'default' => $field->default,
                'hint' => $field->hint
            ];
        }
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->model->setAttribute('name', $this->name);

        if ($this->model->save()) {
            $this->model->unlinkAll('customFormFields', true);
            foreach ($this->fields as $field) {
                $model = new CustomFormField();
                $model->setAttributes($field);
                if (isset($field['options'])) {
                    $model->options = json_encode($field['options']);
                } else {
                    $model->options = json_encode([]);
                }
                $this->model->link('customFormFields', $model);
            }
            return true;
        }
        return false;
    }
}
