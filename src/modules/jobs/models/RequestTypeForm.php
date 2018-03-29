<?php

namespace app\modules\jobs\models;

use app\models\RequestType;
use app\models\RequestTypeDefaultCostCenter;
use yii\base\Model;
use yii\db\ActiveQuery;

class RequestTypeForm extends Model
{
    /**
     * @var RequestType
     */
    public $model;

    public $name;
    public $custom_form_id;
    public $alert;
    public $show_cwa = true;
    public $show_links = true;
    public $show_cost_center = true;
    public $show_mandate = true;
    public $show_translation_needed = true;
    public $sort = 0;

    public $defaultCostCenters = [];

    public function rules()
    {
        return [
            [
                [
                    'custom_form_id',
                    'show_cwa',
                    'show_links',
                    'show_cost_center',
                    'show_mandate',
                    'show_translation_needed',
                    'sort'
                ],
                'integer'
            ],
            [['defaultCostCenters'], 'safe'],
            [['name'], 'required'],
            [['alert'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    public function setModel(RequestType $model)
    {
        $this->model = $model;
        $this->setAttributes($model->getAttributes());
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->model->setAttributes($this->getAttributes());

        if ($this->model->save()) {
            $this->model->unlinkAll('requestTypeDefaultCostCenters', true);
            foreach ($this->defaultCostCenters as $field) {
                $model = new RequestTypeDefaultCostCenter();
                $model->setAttributes($field);
                $this->model->link('requestTypeDefaultCostCenters', $model);
            }
            return true;
        }
        return false;
    }
}
