<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'pin',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'is_active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    public function isBeachOwner(): bool
    {
        return $this->role === 'beach_owner';
    }

    public function assignedLocations()
    {
        return $this->belongsToMany(Location::class, 'admin_location_assignments', 'admin_id', 'location_id')
            ->withPivot(['assigned_by', 'assigned_at', 'expires_at', 'is_active'])
            ->wherePivot('is_active', true);
    }

    public function featureRequests()
    {
        return $this->hasMany(FeatureRequest::class);
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
