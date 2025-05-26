<?php

use TTM\DevPanel\FieldStrategies\EnumFieldStrategy;
use TTM\DevPanel\Attribute;
use Workbench\App\Values\TestEnum;

describe('isApplicable', function () {
    it('returns true if cast is an enum', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'status',
            dbType: 'string',
            cast: TestEnum::class,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new EnumFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns false if cast is not an enum', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'status',
            dbType: 'string',
            cast: 'NotARealEnum',
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new EnumFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getFieldCode', function () {
    it('returns correct field code for enum', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'status',
            dbType: 'string',
            cast: TestEnum::class,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new EnumFieldStrategy();
        expect($strategy->getFieldCode($attribute))->toMatchSnapshot();
    });
});
