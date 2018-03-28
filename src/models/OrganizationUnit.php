<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "organization_unit".
 *
 * @property integer $id
 * @property string $name
 * @property integer $order
 * @property boolean $ask_for_input
 *
 * @property User[] $users
 */
class OrganizationUnit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'organization_unit';
    }

    public static function getDataList($withEmpty = false, $emptyValue = '', $emptyLabel = 'None')
    {
        $out = ArrayHelper::map(static::find()->orderBy(['order' => SORT_ASC])->all(), 'id', function ($item) {
            /** @var OrganizationUnit $item */
            return $item->name;
        });
        if (!$withEmpty) {
            return $out;
        }
        return [$emptyValue => $emptyLabel] + $out;
    }

    public static function getDataAttributes()
    {
        $out = ArrayHelper::map(static::find()->orderBy(['order' => SORT_ASC])->all(), 'id', function ($item) {
            /** @var OrganizationUnit $item */
            return [
                'data-freeform' => $item->ask_for_input
            ];
        });
        return $out;

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['order'], 'integer'],
            [['ask_for_input'], 'boolean'],
            [['name', 'order'], 'required'],
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
            'order' => 'Order',
            'ask_for_input' => 'Ask for free-form input',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['organization_unit_id' => 'id']);
    }
}