<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function sprints(): BelongsToMany
    {
        return $this->belongsToMany(
            Sprint::class,
            'sprint_members',
            'user_id',
            'sprint_id'
        );
    }

    public function onetimepassword(): HasMany
    {
        return $this->hasMany(OneTimePassword::class);
    }

    public function isSprintMember(string $taskSlug): bool
    {
        $task = Task::where('slug', $taskSlug)->first();
        if ($task) {
            return $task->sprint->members->contains('user_id', $this->id);
        }

        return false;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($user){
            $user->id = (string)Str::uuid();
        });
    }
}
