<?php

namespace TTM\DevPanel\ColumnStrategies;

use TTM\DevPanel\Attribute;

interface ColumnStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool;

    public function getColumnCode(Attribute $attribute): string;
}
