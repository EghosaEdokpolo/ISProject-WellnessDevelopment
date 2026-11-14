<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedArticle extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'article_slug'];
}