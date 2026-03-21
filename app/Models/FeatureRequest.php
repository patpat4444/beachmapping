<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_id',
        'title',
        'description',
        'type',
        'status',
        'proposed_amount',
        'admin_notes',
        'approved_by',
        'approved_at',
        'expires_at',
    ];

    protected $casts = [
        'proposed_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
