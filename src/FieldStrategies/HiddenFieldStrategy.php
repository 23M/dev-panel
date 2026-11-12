<?php

declare(strict_types=1);

namespace TTM\DevPanel\FieldStrategies;

use TTM\DevPanel\Attribute;

class HiddenFieldStrategy implements FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return $attribute->isHidden;
    }

    public function getFieldCode(Attribute $attribute): string
    {
        return <<<PHP
            \Filament\Forms\Components\TextInput::make('{$attribute->name}')
                ->password()
                ->revealable()
                ->dehydrated(fn (\$state): bool => filled(\$state)),
        PHP;
    }
}
