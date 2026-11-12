<?php

declare(strict_types=1);

namespace TTM\DevPanel\FieldStrategies;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use TTM\DevPanel\Attribute;

class CastedFieldStrategy implements FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return is_subclass_of($attribute->cast, CastsAttributes::class);
    }

    public function getFieldCode(Attribute $attribute): string
    {
        return <<<PHP
            \Filament\Forms\Components\TextInput::make('{$attribute->name}')
                ->formatStateUsing(fn (\$state, ?\Illuminate\Database\Eloquent\Model \$record, \Filament\Schemas\Components\Component \$component, \\{$attribute->cast} \$caster): ?string => \TTM\DevPanel\CastedRecord::format(\$caster, \$record, \\{$attribute->modelClass}::class, \$component, '{$attribute->name}', \$state))
                ->dehydrateStateUsing(fn (\$state, ?\Illuminate\Database\Eloquent\Model \$record, \Filament\Schemas\Components\Component \$component, \\{$attribute->cast} \$caster): mixed => \TTM\DevPanel\CastedRecord::parse(\$caster, \$record, \\{$attribute->modelClass}::class, \$component, '{$attribute->name}', \$state)),
        PHP;
    }
}
