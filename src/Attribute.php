<?php

declare(strict_types=1);

namespace TTM\DevPanel;

readonly class Attribute
{
    public function __construct(
        public string $modelClass,
        public string $name,
        public string $dbType,
        public ?string $cast,
        public ?string $belongsToRelation,
        public bool $hasTranslations,
    ) {
    }
}
