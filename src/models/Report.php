<?php

namespace app\models;

/**
 * This is the model class for table "report".
 *
 * @property integer $id
 * @property string $name
 * @property string $content
 * @property integer $owner_id
 * @property integer $public
 * @property integer $processing_unit_id
 * @property string $footnote
 * @property string $filters
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $owner
 */
class Report extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'content', 'owner_id'], 'required'],
            [['content'], 'string'],
            [['owner_id', 'public', 'processing_unit_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['footnote', 'filters'], 'safe'],
            [
                ['owner_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['owner_id' => 'id']
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
            'name' => 'Name',
            'content' => 'Content',
            'owner_id' => 'Owner ID',
            'public' => 'Public',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::class, ['id' => 'owner_id']);
    }

    public function getDecodedContent()
    {
        return json_decode($this->content, true);
    }

    public function getDecodedFilters(): array
    {
        return json_decode($this->filters ?? '{}', true);
    }
}
