<?php

use TTM\DevPanel\Attribute;
use TTM\DevPanel\ColumnStrategies\HiddenColumnStrategy;

function hiddenColumnAttribute(bool $isHidden): Attribute
{
    return new Attribute(
        modelClass: 'App\Models\User',
        name: 'password',
        dbType: 'string',
        cast: null,
        belongsToRelation: null,
        hasTranslations: false,
        isHidden: $isHidden,
    );
}

describe('isApplicable', function () {
    it('is applicable to hidden attributes', function () {
        expect((new HiddenColumnStrategy())->isApplicable(hiddenColumnAttribute(true)))->toBeTrue();
    });

    it('is not applicable to visible attributes', function () {
        expect((new HiddenColumnStrategy())->isApplicable(hiddenColumnAttribute(false)))->toBeFalse();
    });
});

describe('getColumnCode', function () {
    it('shows a mask instead of reading the attribute', function () {
        expect((new HiddenColumnStrategy())->getColumnCode(hiddenColumnAttribute(true)))
            ->toMatchSnapshot();
    });
});
