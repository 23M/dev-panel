<?php

use TTM\DevPanel\FieldStrategies\IntegerFieldStrategy;
use TTM\DevPanel\Attribute;

describe('isApplicable', function () {
    it('returns true for integer cast', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'count',
            dbType: 'string',
            cast: 'integer',
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new IntegerFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns true for integer dbType', function () {
        foreach (['int', 'integer', 'bigint', 'smallint', 'mediumint'] as $type) {
            $attribute = new Attribute(
                modelClass: 'App\\Models\\Test',
                name: 'count',
                dbType: $type,
                cast: null,
                belongsToRelation: null,
                hasTranslations: false,
            );
            $strategy = new IntegerFieldStrategy();
            expect($strategy->isApplicable($attribute))->toBeTrue();
        }
    });

    it('returns false for non-integer', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'count',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new IntegerFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getFieldCode', function () {
    it('returns correct field code', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'count',
            dbType: 'int',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new IntegerFieldStrategy();
        expect($strategy->getFieldCode($attribute))->toMatchSnapshot();
    });
});
