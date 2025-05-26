<?php

describe('isApplicable', function () {
    it('is applicable if the attribute has a cast', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: 'App\Models\User',
            name: 'name',
            dbType: 'string',
            cast: \Workbench\App\Casts\CustomCast::class,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\ColumnStrategies\CastedColumnStrategy();

        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('is not applicable if the attribute does not have a cast', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: 'App\Models\User',
            name: 'name',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\ColumnStrategies\CastedColumnStrategy();

        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getColumnCode', function () {
    it('returns the correct column code', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: 'App\Models\User',
            name: 'name',
            dbType: 'string',
            cast: \Workbench\App\Casts\CustomCast::class,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\ColumnStrategies\CastedColumnStrategy();

        expect($strategy->getColumnCode($attribute))
            ->toMatchSnapshot();
    });
});

