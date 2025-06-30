<?php

namespace TTM\DevPanel\FieldStrategies;

use TTM\DevPanel\Attribute;

class EnumFieldStrategy implements FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return $attribute->cast && enum_exists($attribute->cast);
    }

    public function getFieldCode(Attribute $attribute): string
    {
        $enumClass = '\\'.$attribute->cast;

        return <<<PHP
            \Filament\Forms\Components\Select::make('{$attribute->name}')
                ->options($enumClass::class),
        PHP;
    }
}
