<?php

use TTM\DevPanel\FieldStrategies\DateTimeFieldStrategy;
use TTM\DevPanel\Attribute;

describe('isApplicable', function () {
    it('returns true for dbType "datetime"', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'created_at',
            dbType: 'datetime',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateTimeFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns true for dbType "timestamp"', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'created_at',
            dbType: 'timestamp',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateTimeFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns true for cast "datetime"', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'created_at',
            dbType: 'string',
            cast: 'datetime',
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateTimeFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns true for cast "immutable_datetime"', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'created_at',
            dbType: 'string',
            cast: 'immutable_datetime',
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateTimeFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns true for cast "timestamp"', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'created_at',
            dbType: 'string',
            cast: 'timestamp',
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateTimeFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns false for other types', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'created_at',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateTimeFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getFieldCode', function () {
    it('returns correct field code', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'created_at',
            dbType: 'datetime',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DateTimeFieldStrategy();
        expect($strategy->getFieldCode($attribute))
            ->toMatchSnapshot();
    });
});
