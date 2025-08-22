<?php

namespace app\models;

use app\models\dto\LinkDto;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * @property int $id
 * @property string $original_url
 * @property string|null $short_code
 * @property int $clicks
 * @property int|null $updated_at
 * @property int $created_at
 */
final class Link extends ActiveRecord
{
    public static function create(LinkDto $dto): self
    {
        $model = new self();
        $model->short_code = $dto->short_code;
        $model->original_url = $dto->original_url;
        $model->clicks = 0;
        return $model;
    }

    public static function tableName(): string
    {
        return '{{%links}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }
}
