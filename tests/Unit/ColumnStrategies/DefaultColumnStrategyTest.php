<?php

describe('isApplicable', function () {
    it('is always applicable', function () {
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
