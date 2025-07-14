<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = "categories";
    protected $fillable = [
        'name',
        'slug',
        'created_by',
        'updated_by'
    ];

    protected $keyType = 'string';
    public $incrementing = false;
    
    public function creator(){
        return $this->belongsTo(User::class, 'created_by');
    }
    public function editor(){
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function article(){
        return $this->belongsToMany(Article::class, 'article_categories', 'category_id', 'article_id');
    }
    public function tags(){
        return $this->belongsToMany(Tags::class, 'tag_categories', 'category_id', 'tag_id');
    }


}
