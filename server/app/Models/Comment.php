<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable=[
        'user_id',
        'post_id',
        'body'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
