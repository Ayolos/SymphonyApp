<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\In;
use Inertia\Inertia;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
