<?php

declare(strict_types=1);

namespace TTM\DevPanel\FieldStrategies;

use TTM\DevPanel\Attribute;

class JsonFieldStrategy implements FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return $attribute->cast === 'array';
    }

    public function getFieldCode(Attribute $attribute): string
    {
        return <<<PHP
            \Filament\Forms\Components\Textarea::make('{$attribute->name}')
                ->formatStateUsing(fn (\$state) => \$state === null ? null : json_encode(\$state))
                ->dehydrateStateUsing(fn (\$state) => is_string(\$state) && \$state !== '' ? json_decode(\$state, true) : null)
                ->json(),
        PHP;
    }
}
