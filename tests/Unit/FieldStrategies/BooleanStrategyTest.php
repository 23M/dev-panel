<?php

describe('isApplicable', function () {
    it('is applicable if the db type is boolean', function (string $dbType) {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'boolean',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\BooleanFieldStrategy();

        expect($strategy->isApplicable($attribute))->toBeTrue();
    })->with([
        'boolean',
        'tinyint(1)',
        'bit(1)',
    ]);

    it('is applicable if the cast is boolean', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'string',
            cast: 'boolean',
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\BooleanFieldStrategy();

        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('is not applicable if the db type is not boolean', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\BooleanFieldStrategy();

        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getFieldCode', function () {
    it('returns the correct field code for a boolean attribute', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'boolean',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\BooleanFieldStrategy();

        expect($strategy->getFieldCode($attribute))
            ->toMatchSnapshot();
    });
});
