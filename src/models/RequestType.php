<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "request_type".
 *
 * @property integer $id
 * @property integer $processing_unit_id
 * @property string $name
 * @property integer $custom_form_id
 * @property integer $show_cwa
 * @property integer $show_links
 * @property integer $show_cost_center
 * @property integer $show_mandate
 * @property integer $show_translation_needed
 * @property string $alert
 * @property integer $sort
 *
 * @property ProcessingUnit $processingUnit
 * @property RequestTypeDefaultCostCenter[] $requestTypeDefaultCostCenters
 * @property CustomForm $customForm
 */
class RequestType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_type';
    }

    public static function getDataList($processing_unit, $withCustomFormOnly = false)
    {
        $out = [];
        if ($processing_unit == null) {
            foreach (ProcessingUnit::getDataList() as $key => $value) {
                $out += ArrayHelper::map(static::find()->where(['processing_unit_id' => $key])->all(), 'id',
                    function ($item) use ($value) {
                        return $value . ' - ' . $item->name;
                    });
            }
        } else {
            $out = ArrayHelper::map(collect(static::find()->with('customForm')->where(['processing_unit_id' => $processing_unit])->all())
                ->filter(function (RequestType $item) use ($withCustomFormOnly) {
                    if ($withCustomFormOnly) {
                        return $item->customForm !== null;
                    }
                    return true;
                })->all(), 'id',
                'name');
        }

        return $out;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'processing_unit_id',
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
            [['name'], 'required'],
            [['alert'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [
                ['processing_unit_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ProcessingUnit::class,
                'targetAttribute' => ['processing_unit_id' => 'id']
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
            'processing_unit_id' => 'Processing Department',
            'name' => 'Name',
            'custom_form_id' => 'Custom Form',
            'show_cwa' => 'Show Cwa',
            'show_links' => 'Show Links',
            'show_cost_center' => 'Show Cost Center',
            'show_mandate' => 'Show Mandate',
            'show_translation_needed' => 'Show Translation Needed',
            'alert' => 'Alert content on job creation',
            'sort' => 'Sort'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomForm()
    {
        return $this->hasOne(CustomForm::class, ['id' => 'custom_form_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessingUnit()
    {
        return $this->hasOne(ProcessingUnit::class, ['id' => 'processing_unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestTypeDefaultCostCenters()
    {
        return $this->hasMany(RequestTypeDefaultCostCenter::class, ['request_type_id' => 'id']);
    }
}