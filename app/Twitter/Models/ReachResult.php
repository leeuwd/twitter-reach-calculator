<?php
declare(strict_types=1);

namespace App\Twitter\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Coduo\PHPHumanizer\NumberHumanizer;

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

    /**
     * Collection of retweeters.
     *
     * @var Collection
     */
    public $retweeters;

    /**
     * Retweeters count.
     *
     * @var int;
     */
    public $retweetersCount;

    /**
     * Reach metric.
     *
     * @var int;
     */
    public $reach;

    /**
     * Boa constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Initialize internal variables
        $this->init();
    }

    /**
     * Initialize.
     */
    protected function init(): void
    {
        // New empty collection; counters are here for
        // performance improvement since it prevents
        // looping through the collection and plucking
        // data
        $this->reach = 0;
        $this->retweetersCount = 0;
        $this->retweeters = Collection::make();
    }

    /**
     * Add retweeter to collection.
     *
     * @param \App\Twitter\Models\User $retweeter
     * @return \App\Twitter\Models\ReachResult
     */
    public function addRetweeter(User $retweeter): ReachResult
    {
        // Add to collection
        $this->retweeters->push($retweeter);

        // Increment internal counters (i.e. performance improvements);
        // use defensive programming in the sense that if follower count
        // is not set, just use zero
        $this->reach += $retweeter->followers_count ?? 0;
        $this->retweetersCount++;

        return $this;
    }

    /**
     * Get humanized number of reach metric,
     * i.e. 2K, 1M or just plain 10 for the
     * less popular Tweets.
     *
     * @return string
     */
    public function getHumanizedReachMetric(): string
    {
        return NumberHumanizer::metricSuffix($this->reach);
    }

    /**
     * Return result data as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'reach'           => $this->reach,
            'humanizedReach'  => $this->getHumanizedReachMetric(),
            'hasRetweets'     => $this->hasRetweets ?? false,
            'retweetersCount' => $this->retweetersCount ?? 0,
            'tweet'           => $this->tweet->toArray(),
        ];
    }
}
