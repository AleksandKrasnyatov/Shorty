<?php

namespace app\models\dto;

final readonly class LinkDto
{
    public function __construct(
        public string $original_url,
        public string $short_code,
    )
    {
    }
}