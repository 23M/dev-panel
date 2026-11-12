<?php

declare(strict_types=1);

namespace TTM\DevPanel\ColumnStrategies;

use TTM\DevPanel\Attribute;

/**
 * Hidden attributes (passwords, tokens, secrets) must not be read by the table, so it only shows a mask.
 */
class HiddenColumnStrategy implements ColumnStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return $attribute->isHidden;
    }

    public function getColumnCode(Attribute $attribute): string
    {
        return <<<PHP
            TextColumn::make('{$attribute->name}')
                ->state('••••••'),
            PHP;
    }
}
