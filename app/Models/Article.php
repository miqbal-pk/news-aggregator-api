<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    /**
     * Theses attributes are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_id', 'source_name', 'author', 'category', 'title', 'description', 'url', 'url_to_image', 'content', 'published_at'
    ];
}
