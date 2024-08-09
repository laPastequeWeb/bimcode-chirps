<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
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

    public function posts():HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class, 
            'subscriptions', 
            'user_id', 
            'subscribed_user_id'
        );
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class, 
            'subscriptions', 
            'subscribed_user_id', 
            'user_id'
        );
    }
}
