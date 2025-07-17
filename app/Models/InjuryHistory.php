<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class InjuryHistory extends Model
{
    use HasFactory,HasRoles,HasUuids;  // Remove HasUuids

    protected $table = "InjuryHistory";
    protected $fillable = [
        'user_id',
        'label',
        'image',
        'detected_at',
        'notes',
        'location',
        'scores',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'detected_at' => 'datetime',
    ];

    protected $keyType = 'string';
    public $incrementing = false;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
