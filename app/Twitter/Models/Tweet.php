<?php
declare(strict_types=1);

namespace App\Twitter\Models;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'entities' => 'array',
    ];
}
