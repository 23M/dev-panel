<?php

use TTM\DevPanel\Attribute;
use TTM\DevPanel\FieldStrategies\HiddenFieldStrategy;

describe('isApplicable', function () {
    it('is applicable if the attribute is hidden by the model', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'password',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
            isHidden: true,
        );
        $strategy = new HiddenFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('is not applicable if the attribute is not hidden', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'name',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );
        $strategy = new HiddenFieldStrategy();
        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getFieldCode', function () {
    it('returns a write-only field that keeps the stored value when left empty', function () {
        $attribute = new Attribute(
            modelClass: 'App\\Models\\Test',
            name: 'password',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
            isHidden: true,
        );
        $strategy = new HiddenFieldStrategy();
        expect($strategy->getFieldCode($attribute))->toMatchSnapshot();
    });
});
