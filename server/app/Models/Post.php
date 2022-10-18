<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable= [
        'user_id',
        'image',
        'title',
        'body'
    ];

    public function categories() {
        return $this->belongsToMany(Category::class,'category_post');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function images() {
        return $this->hasMany(PostImage::class);
    }
}
