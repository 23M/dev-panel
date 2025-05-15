<?php

declare(strict_types=1);

namespace TTM\DevPanel\FieldStrategies;

use TTM\DevPanel\Attribute;

class DateTimeFieldStrategy implements FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return in_array($attribute->dbType, ['datetime', 'timestamp'])
            || in_array($attribute->cast, ['datetime', 'immutable_datetime', 'timestamp']);
    }

    public function getFieldCode(Attribute $attribute): string
    {
        return <<<PHP
            \Filament\Forms\Components\DatePicker::make('{$attribute->name}'),
        PHP;
    }
}
