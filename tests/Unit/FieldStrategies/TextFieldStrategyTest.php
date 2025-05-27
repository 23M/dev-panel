<?php

describe('isApplicable', function () {
    it('is applicable if the db type text', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'text',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\TextFieldStrategy();

        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('is not applicable if the db type is not text', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\TextFieldStrategy();

        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getFieldCode', function () {
    it('returns the correct field code for a boolean attribute', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'text',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\TextFieldStrategy();

        expect($strategy->getFieldCode($attribute))
            ->toMatchSnapshot();
    });
});
