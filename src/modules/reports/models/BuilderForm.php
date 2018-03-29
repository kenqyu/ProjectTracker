<?php

namespace app\modules\reports\models;

use app\models\Report;
use yii\base\Model;

class BuilderForm extends Model
{
    /**
     * @var Report
     */
    private $model;

    public $name;
    public $processing_unit_id;
    public $footnote;
    public $public = true;
    public $content = [];
    public $filters = [];

    public function rules()
    {
        return [
            [['name'], 'string'],
            [['public'], 'boolean'],
            [['name'], 'required'],
            [['content', 'footnote', 'filters'], 'safe'],
            [['processing_unit_id'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'processing_unit_id' => 'Processing Unit'
        ];
    }

    public function setModel(Report $report)
    {
        $this->model = $report;
        $this->name = $report->name;
        $this->public = $report->public;
        $this->processing_unit_id = $report->processing_unit_id;
        $this->footnote = $report->footnote;
        $this->content = $report->getDecodedContent();
        $this->filters = $report->getDecodedFilters();
    }

    public function save()
    {
        if (!$this->model) {
            $this->model = new Report();
            $this->model->owner_id = \Yii::$app->user->id;
        }
        $this->model->name = $this->name;
        $this->model->content = json_encode($this->content);
        $this->model->filters = json_encode($this->filters);
        $this->model->public = $this->public;
        $this->model->footnote = $this->footnote;
        $this->model->processing_unit_id = $this->processing_unit_id;

        return $this->model->save();
    }

    public function getId()
    {
        return $this->model->id;
    }

    public function isNewRecord()
    {
        return !$this->model ?? $this->model->isNewRecord;
    }

    public function getModel(): Report
    {
        return $this->model ?? new Report();
    }
}
