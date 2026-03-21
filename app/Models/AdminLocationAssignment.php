<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLocationAssignment extends Model
{
    use HasFactory;

    protected $table = 'admin_location_assignments';

    protected $fillable = [
        'admin_id',
        'location_id',
        'assigned_by',
        'assigned_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
