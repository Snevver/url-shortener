<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortenedUrl extends Model
{
    protected $fillable = ['slug', 'original_url'];
}
