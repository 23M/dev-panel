<?php

declare(strict_types=1);

namespace TTM\DevPanel\FieldStrategies;

use TTM\DevPanel\Attribute;

class DateFieldStrategy implements FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return in_array($attribute->dbType, ['date'])
            || in_array($attribute->cast, ['date', 'immutable_date']);
    }

    public function getFieldCode(Attribute $attribute): string
    {
        return <<<PHP
            \Filament\Forms\Components\DatePicker::make('{$attribute->name}'),
        PHP;
    }
}
