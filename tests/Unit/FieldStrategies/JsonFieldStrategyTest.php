<?php

use TTM\DevPanel\FieldStrategies\JsonFieldStrategy;
use TTM\DevPanel\Attribute;

describe('isApplicable', function () {
    it('returns true for array cast', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'meta',
            dbType: 'json',
            cast: 'array',
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new JsonFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns false for non-array cast', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'meta',
            dbType: 'json',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new JsonFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getFieldCode', function () {
    it('returns correct field code', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'meta',
            dbType: 'json',
            cast: 'array',
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new JsonFieldStrategy();
        expect($strategy->getFieldCode($attribute))->toMatchSnapshot();
    });
});
