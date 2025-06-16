<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Tags extends Model
{
    //
    use HasFactory,HasRoles;
    protected $table = "tags";
    protected $fillable = [
        'name',
        'slug',
        'status',
        'created_by',
        'updated_by',
    ];
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');

    }
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function tags()
    {
        return $this->belongsToMany(Article::class, 'article_tags','article_id','tag_id');
    }
    
}
