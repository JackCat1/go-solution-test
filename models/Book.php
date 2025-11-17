<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Book ActiveRecord.
 *
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string|null $description
 * @property int|null $year
 * @property int|null $created_by
 * @property int $created_at
 * @property int $updated_at
 */
class Book extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%book}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['title', 'author'], 'required'],
            [['description'], 'string'],
            [['year', 'created_by'], 'integer'],
            [['year'], 'integer', 'min' => 0, 'max' => (int) date('Y') + 2],
            [['title', 'author'], 'string', 'max' => 255],
            [
                ['created_by'],
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => ['created_by' => 'id'],
                'skipOnEmpty' => true,
            ],
        ];
    }

    public function fields(): array
    {
        return [
            'id',
            'title',
            'author',
            'description',
            'year',
            'created_at',
            'updated_at',
            'created_by',
        ];
    }

    public function extraFields(): array
    {
        return [
            'creator' => fn () => $this->creator ? [
                'id' => $this->creator->id,
                'username' => $this->creator->username,
                'email' => $this->creator->email,
            ] : null,
        ];
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
}
