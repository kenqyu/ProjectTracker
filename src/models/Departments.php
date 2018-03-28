<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "departments".
 *
 * @property integer $id
 * @property string $name
 * @property integer $organization_unit_id
 *
 * @property SubDepartment[] $subDepartments
 *
 */
class Departments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'departments';
    }

    public static function getDataList(
        $withEmpty = false,
        $emptyLabel = 'None',
        $include = null,
        $withRequestTypesOnly = false
    ) {
        $out = collect(ArrayHelper::map(static::find()->all(), 'id', 'name'))
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

    public static function getDataListByOrganizationUnit($id)
    {
        return ArrayHelper::map(static::find()->where(['organization_unit_id' => $id])->all(), 'id', 'name');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['organization_unit_id'], 'integer'],
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
            'organization_unit_id' => 'Organization Unit'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubDepartments()
    {
        return $this->hasMany(SubDepartment::class, ['department_id' => 'id']);
    }
}