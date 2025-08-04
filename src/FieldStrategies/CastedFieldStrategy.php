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
                ->formatStateUsing(fn (\$state, \Illuminate\Database\Eloquent\Model \$record, \\{$attribute->cast} \$caster): string => (\$caster->set(\$record, '{$attribute->name}', \$state, \$record->toArray()) ?? ''))
                ->dehydrateStateUsing(fn (\$state, \Illuminate\Database\Eloquent\Model \$record, \\{$attribute->cast} \$caster): mixed => (\$caster->get(\$record, '{$attribute->name}', \$state, \$record->toArray()) ?? '')),
        PHP;
    }
}
