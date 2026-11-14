<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBookmark extends Model
{
    protected $fillable = ['user_id', 'article_slug'];
}