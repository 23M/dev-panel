<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Workbench\App\Values\TestEnum;
use Workbench\Database\Factories\PostFactory;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => TestEnum::class,
            'is_published' => 'boolean',
            'views' => 'integer',
            'meta' => 'array',
            'published_on' => 'date',
            'published_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
