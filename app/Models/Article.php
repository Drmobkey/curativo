<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Article extends Model
{
    //

    use HasFactory,HasRoles;
    protected $table = "articles";
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'status',
        'created_by',
        'updated_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
        
    }
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_categories');
    }

    public function tags()
    {
        return $this->belongsToMany(Tags::class, 'article_tags','article_id','tag_id');
    }
}
