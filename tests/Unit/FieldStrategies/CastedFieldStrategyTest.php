<?php

describe('isApplicable', function () {
    it('is applicable if a custom cast is given', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'string',
            cast: \Workbench\App\Casts\CustomCast::class,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\CastedFieldStrategy();

        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('is not applicable if no custom cast is given', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\CastedFieldStrategy();

        expect($strategy->isApplicable($attribute))->toBeFalse();
    });

    it('is not applicable if the cast is not a subclass of CastsAttributes', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'string',
            cast: 'string',
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\CastedFieldStrategy();

        expect($strategy->isApplicable($attribute))->toBeFalse();
    });

    describe('getFieldCode', function () {
        it('returns the correct field code for a custom cast', function () {
            $attribute = new TTM\DevPanel\Attribute(
                modelClass: \Workbench\App\Models\User::class,
                name: 'test',
                dbType: 'string',
                cast: \Workbench\App\Casts\CustomCast::class,
                belongsToRelation: null,
                hasTranslations: false,
            );

            $strategy = new TTM\DevPanel\FieldStrategies\CastedFieldStrategy();

            expect($strategy->getFieldCode($attribute))
                ->toMatchSnapshot();
        });
    });
});
