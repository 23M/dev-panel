<?php

use TTM\DevPanel\FieldStrategies\DateFieldStrategy;
use TTM\DevPanel\Attribute;

describe('isApplicable', function () {
    it('returns true for dbType "date"', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'birthday',
            dbType: 'date',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns true for cast "date"', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'birthday',
            dbType: 'string',
            cast: 'date',
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns true for cast "immutable_date"', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'birthday',
            dbType: 'string',
            cast: 'immutable_date',
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns false for other types', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'birthday',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getFieldCode', function () {
    it('returns correct field code', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'birthday',
            dbType: 'date',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateFieldStrategy();
        expect($strategy->getFieldCode($attribute))
            ->toMatchSnapshot();
    });
});
