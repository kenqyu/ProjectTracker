<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "request_type_default_cost_center".
 *
 * @property integer $id
 * @property integer $request_type_id
 * @property string $cost_center_label
 * @property integer $cost_center_percent
 *
 * @property RequestType $requestType
 */
class RequestTypeDefaultCostCenter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_type_default_cost_center';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request_type_id', 'cost_center_percent'], 'integer'],
            [['cost_center_label'], 'string', 'max' => 255],
            [
                ['request_type_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => RequestType::class,
                'targetAttribute' => ['request_type_id' => 'id']
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
            'request_type_id' => 'Request Type ID',
            'cost_center_label' => 'Cost Center Label',
            'cost_center_percent' => 'Cost Center Percent',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestType()
    {
        return $this->hasOne(RequestType::class, ['id' => 'request_type_id']);
    }
}