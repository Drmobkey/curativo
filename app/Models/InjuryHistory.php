<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InjuryHistory extends Model
{
    use HasFactory;  // Remove HasUuids

    protected $table = "InjuryHistory";
    protected $fillable = [
        'user_id',
        'label',
        'image',
        'detected_at',
        'notes',
        'location',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'detected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
