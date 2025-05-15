<?php

declare(strict_types=1);

namespace TTM\DevPanel\ColumnStrategies;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use TTM\DevPanel\Attribute;

class CastedColumnStrategy implements ColumnStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return is_subclass_of($attribute->cast, CastsAttributes::class);
    }

    public function getColumnCode(Attribute $attribute): string
    {
        return <<<PHP
            TextColumn::make('{$attribute->name}')
                ->getStateUsing(fn (\Illuminate\Database\Eloquent\Model \$record): string => (\$record->getRawOriginal('{$attribute->name}') ?? ''))
                ->limit(50)
                ->searchable()
                ->sortable(),
            PHP;
    }
}
