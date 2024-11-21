<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Enum\SprintStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sprint extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'start_at',
        'end_at',
        'status',
        'is_completed'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SprintStatusEnum::class,
        ];
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'sprint_members',
            'sprint_id',
            'user_id',
        )->withPivot('is_admin', 'is_creator')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function isCreator(User $user): bool
    {
        return $this->members()
            ->wherePivot('user_id', $user->id)
            ->wherePivot('is_admin', true)
            ->exists();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($sprint){
            $sprint->id = (string)Str::uuid();
            $slug = Str::of($sprint->name)->slug();
            $sprint->slug = Sprint::whereSlug($slug)->exists() ? $slug->append('-', rand()) : $slug;
        });
    }
}
