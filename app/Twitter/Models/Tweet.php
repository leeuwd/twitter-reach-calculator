<?php
declare(strict_types=1);

namespace App\Twitter\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Twitter\Models\Tweet
 *
 * @property int $id
 * @property string $text
 * @property array $entities
 * @property string $source
 * @property string $lang
 * @property bool $retweeted
 * @property int $retweet_count
 * @property bool $favorited
 * @property bool $possibly_sensitive
 * @property bool $possibly_sensitive_appealable
 * @property bool $is_quote_status
 * @property \App\Twitter\Models\User|null $user
 * @property \Carbon\Carbon|null $created_at
 */
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
