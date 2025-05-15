<?php

declare(strict_types=1);

namespace TTM\DevPanel\FieldStrategies;

use TTM\DevPanel\Attribute;

class DefaultFieldStrategy implements FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return true;
    }

    public function getFieldCode(Attribute $attribute): string
    {
        return <<<PHP
            \Filament\Forms\Components\TextInput::make('$attribute->name'),
        PHP;
    }
}
