<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "departments".
 *
 * @property integer $id
 * @property string $name
 * @property integer $order
 *
 * @property RequestType[] $requestTypes
 *
 */
class ProcessingUnit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'processing_unit';
    }

    public static function getDataList(
        $withEmpty = false,
        $emptyLabel = 'None',
        $include = null,
        $withRequestTypesOnly = false
    ) {
        $out = collect(ArrayHelper::map(static::find()->orderBy(['order' => SORT_ASC])->all(), 'id', 'name'))
            ->filter(function ($item, $key) use ($include) {
                if ($include === null) {
                    return true;
                }
                return in_array($key, $include);
            });
        if ($withRequestTypesOnly) {
            $out = $out->filter(function ($item, $key) {
                return empty($item->requestTypes);
            });
        }
        if (!$withEmpty) {
            return $out->all();
        }

        return ['' => $emptyLabel] + $out->all();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['order'], 'integer'],
            [['order'], 'default', 'value' => 0],
            [['name'], 'unique']
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
            'order' => 'Order'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestTypes()
    {
        return $this->hasMany(RequestType::class, ['processing_init_id' => 'id']);
    }
}