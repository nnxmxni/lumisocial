<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'sprint_id',
        'user_id',
        'title',
        'content',
        'slug'
    ];

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($task){
            $task->id = (string)Str::uuid();
            $slug = Str::of('task-'.Str::random(4))->slug();
            $task->slug = Task::whereSlug($slug)->exists() ? $slug->append('-', rand()) : $slug;
        });
    }
}
