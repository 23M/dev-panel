<?php

declare(strict_types=1);

namespace Workbench\App\Models;

class Post extends \Illuminate\Database\Eloquent\Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
