<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use TTM\DevPanel\Attribute;
use TTM\DevPanel\FieldStrategies\BelongsToFieldStrategy;

class TitleTestOwner extends Model
{
    protected $table = 'title_test_owners';
}

class TitleTestItem extends Model
{
    protected $table = 'title_test_items';

    /** @return BelongsTo<TitleTestOwner, $this> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(TitleTestOwner::class, 'owner_id');
    }
}

function titleOf(): string
{
    $code = (new BelongsToFieldStrategy())->getFieldCode(new Attribute(
        modelClass: TitleTestItem::class,
        name: 'owner_id',
        dbType: 'integer',
        cast: null,
        belongsToRelation: 'owner',
        hasTranslations: false,
    ));

    preg_match("/relationship\\('owner', '(\\w+)'\\)/", $code, $matches);

    return $matches[1];
}

afterEach(function () {
    Schema::dropIfExists('title_test_items');
    Schema::dropIfExists('title_test_owners');
});

it('does not use a title column that can be empty when another one exists', function () {
    Schema::create('title_test_owners', function ($table) {
        $table->id();
        $table->string('subject');
        $table->text('description')->nullable();
    });
    Schema::create('title_test_items', fn ($table) => $table->id());

    expect(titleOf())->toBe('subject');
});

it('prefers the usual order of title columns', function () {
    Schema::create('title_test_owners', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('subject');
    });
    Schema::create('title_test_items', fn ($table) => $table->id());

    expect(titleOf())->toBe('name');
});

it('falls back to the key when every title column can be empty', function () {
    Schema::create('title_test_owners', function ($table) {
        $table->id();
        $table->text('description')->nullable();
    });
    Schema::create('title_test_items', fn ($table) => $table->id());

    expect(titleOf())->toBe('id');
});
