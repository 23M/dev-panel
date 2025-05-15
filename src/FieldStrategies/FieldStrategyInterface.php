<?php

namespace TTM\DevPanel\FieldStrategies;

use TTM\DevPanel\Attribute;

interface FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool;

    public function getFieldCode(Attribute $attribute): string;
}
