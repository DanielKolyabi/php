<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property boolean $hidden
 */
class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'hidden',
    ];
}
