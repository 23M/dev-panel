<?php

declare(strict_types=1);

namespace TTM\DevPanel\FieldStrategies;

use TTM\DevPanel\Attribute;

class IntegerFieldStrategy implements FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return $attribute->cast === 'integer' || in_array($attribute->dbType, [
            'int',
            'integer',
            'bigint',
            'smallint',
            'mediumint',
        ]);
    }

    public function getFieldCode(Attribute $attribute): string
    {
        return <<<PHP
            \Filament\Forms\Components\TextInput::make('{$attribute->name}')
                ->numeric(),
        PHP;
    }
}
