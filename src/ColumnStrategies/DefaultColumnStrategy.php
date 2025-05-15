<?php

declare(strict_types=1);

namespace TTM\DevPanel\ColumnStrategies;

use TTM\DevPanel\Attribute;

class DefaultColumnStrategy implements ColumnStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return true;
    }

    public function getColumnCode(Attribute $attribute): string
    {
        return <<<PHP
            TextColumn::make('{$attribute->name}')
                ->searchable()
                ->sortable(),
            PHP;
    }
}
