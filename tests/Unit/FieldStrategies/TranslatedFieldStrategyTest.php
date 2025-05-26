<?php

use TTM\DevPanel\FieldStrategies\TranslatedFieldStrategy;
use TTM\DevPanel\Attribute;

describe('isApplicable', function () {
    it('returns true if hasTranslations is true', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'title',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: true,
        );
        $strategy = new TranslatedFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('returns false if hasTranslations is false', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'title',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new TranslatedFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getFieldCode', function () {
    it('returns correct field code', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'title',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: true,
        );
        $strategy = new TranslatedFieldStrategy();
        expect($strategy->getFieldCode($attribute))->toMatchSnapshot();
    });
});
