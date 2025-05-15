<?php

declare(strict_types=1);

namespace TTM\DevPanel\FieldStrategies;

use TTM\DevPanel\Attribute;

class BooleanFieldStrategy implements FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return $attribute->dbType === 'boolean'
            || $attribute->dbType === 'tinyint(1)'
            || $attribute->dbType === 'bit(1)'
            || $attribute->cast === 'boolean';
    }

    public function getFieldCode(Attribute $attribute): string
    {
        return <<<PHP
            \Filament\Forms\Components\Checkbox::make('{$attribute->name}'),
        PHP;
    }
}
