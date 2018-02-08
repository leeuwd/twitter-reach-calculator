<?php
declare(strict_types=1);

namespace App\Twitter\Models;

use Illuminate\Database\Eloquent\Model;

final class ReachResult extends Model
{
    /**
     * Tweet author.
     *
     * @var User|null;
     */
    public $author;

    /**
     * Original Tweet.
     *
     * @var Tweet|null;
     */
    public $tweet;

    /**
     * Original Tweet URL.
     *
     * @var string|null;
     */
    public $tweetUrl;

    /**
     * Flag whether Tweet URL is valid.
     *
     * @var bool;
     */
    public $tweetUrlValid = false;

    /**
     * Original Tweet ID.
     *
     * @var int|null;
     */
    public $tweetId;

    /**
     * Flag whether Tweet ID is valid.
     *
     * @var bool;
     */
    public $tweetIdValid = false;

    /**
     * Flag whether we have retweets at all.
     *
     * @var bool
     */
    public $hasRetweets = false;
}
