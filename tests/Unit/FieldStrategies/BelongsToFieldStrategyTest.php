<?php

describe('isApplicable', function () {
    it('is applicable if a belongs to relation is given', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'string',
            cast: null,
            belongsToRelation: 'relatedModel',
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\BelongsToFieldStrategy();

        expect($strategy->isApplicable($attribute))->toBeTrue();
    });

    it('is not applicable if no belongs to relation is given', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\User::class,
            name: 'test',
            dbType: 'string',
            cast: null,
            belongsToRelation: null,
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\BelongsToFieldStrategy();

        expect($strategy->isApplicable($attribute))->toBeFalse();
    });
});

describe('getFieldCode', function () {
    it('returns the correct field code for a belongs to relation', function () {
        $attribute = new TTM\DevPanel\Attribute(
            modelClass: \Workbench\App\Models\Post::class,
            name: 'test',
            dbType: 'string',
            cast: null,
            belongsToRelation: 'user',
            hasTranslations: false,
        );

        $strategy = new TTM\DevPanel\FieldStrategies\BelongsToFieldStrategy();

        expect($strategy->getFieldCode($attribute))
            ->toMatchSnapshot();
    });
});
