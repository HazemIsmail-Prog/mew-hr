<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'file_number',
        'signature',
        'role',
        'cid',
        'replacement_id',
        'supervisor_id',
        'department_id',
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
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->first();
    }

    public function getSignatureAttribute($value)
    {
        return $value ? asset('storage/signatures/' . $value) : null;
    }

    public function getStampAttribute($value)
    {
        return $value ? asset('storage/stamps/' . $value) : null;
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function replacement()
    {
        return $this->belongsTo(User::class, 'replacement_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    public function missions()
    {
        return $this->hasMany(Mission::class);
    }

    public function permissions()   
    {
        return $this->hasMany(Permission::class);
    }

    public function exemptions()
    {
        return $this->hasMany(Exemption::class);
    }

    public function monthlyMissionsCount($date, $id = null, $status = null)
    {
        return $this->missions()
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->where('id', '!=', $id)
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->count();
    }

    public function monthlyPermissionsCount($date, $id = null, $status = null)
    {
        return $this->permissions()
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->where('id', '!=', $id)
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->count();
    }

    public function monthlyExemptionsCount($date, $id = null, $status = null)
    {
        return $this->exemptions()
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->where('id', '!=', $id)
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->count();
    }

    public function dailyMissionsCount($date, $id = null, $status = null)
    {
        return $this->missions()
            ->whereDate('date', $date)
            ->where('id', '!=', $id)
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->count();
    }

    public function dailyPermissionsCount($date, $id = null, $status = null)
    {
        return $this->permissions()
            ->whereDate('date', $date)
            ->where('id', '!=', $id)
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->count();
    }

    public function dailyExemptionsCount($date, $id = null, $status = null)
    {
        return $this->exemptions()
            ->whereDate('date', $date)
            ->where('id', '!=', $id)
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->count();
    }
    
    
    
    
    
}
