<?php

use TTM\DevPanel\FieldStrategies\DefaultFieldStrategy;
use TTM\DevPanel\Attribute;

describe('isApplicable', function () {
    it('always returns true', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'foo',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DefaultFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });
});

describe('getFieldCode', function () {
    it('returns correct field code', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'foo',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new DefaultFieldStrategy();
        expect($strategy->getFieldCode($attribute))
            ->toMatchSnapshot();
    });
});
