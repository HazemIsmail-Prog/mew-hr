<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /** @use HasFactory<\Database\Factories\PermissionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time',
        'reason',
        'duration',
        'type',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($permission) {
            $permission->user_id = auth()->id();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
