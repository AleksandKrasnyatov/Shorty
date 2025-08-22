<?php

namespace app\models\forms;

use app\models\dto\LinkDto;
use app\models\Link;
use yii\base\Model;

class LinkForm extends Model
{
    public string|null $original_url = null;
    public string|null $short_code = null;

    public function rules(): array
    {
        return [
            [['original_url'], 'required'],
            ['original_url', 'url'],
            ['short_code', 'string', 'max' => 8],
            ['short_code', 'unique', 'targetClass' => Link::class, 'targetAttribute' => ['short_code']],
        ];
    }

    public function toDto(): LinkDto
    {
        return new LinkDto($this->original_url, $this->short_code);
    }
}