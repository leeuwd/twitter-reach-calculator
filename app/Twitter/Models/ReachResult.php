<?php
declare(strict_types=1);

namespace App\Twitter\Models;

use Coduo\PHPHumanizer\NumberHumanizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class ReachResult extends Model
{
    /**
     * Original Tweet.
     *
     * @var Tweet|null;
     */
    protected $tweet;

    /**
     * Original Tweet URL.
     *
     * @var string|null;
     */
    protected $tweetUrl;

    /**
     * Original Tweet ID.
     *
     * @var int|null;
     */
    protected $tweetId;

    /**
     * Collection of retweeters.
     *
     * @var Collection
     */
    protected $retweeters;

    /**
     * Retweeters count.
     *
     * @var int;
     */
    protected $retweetersCount = 0;

    /**
     * Reach metric.
     *
     * @var int;
     */
    protected $reach = 0;

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
     * Initialize internal variables.
     */
    protected function init(): void
    {
        // New empty collection
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
        $this->retweetersCount++;
        $this->reach += $retweeter->followers_count ?? 0;

        return $this;
    }

    /**
     * Set Tweet URL.
     *
     * @param string $url
     * @return \App\Twitter\Models\ReachResult
     */
    public function setTweetUrl(string $url): ReachResult
    {
        $this->tweetUrl = $url;

        return $this;
    }

    /**
     * Get Tweet ID.
     *
     * @return int|null
     */
    public function getTweetId(): ?int
    {
        return $this->tweetId;
    }

    /**
     * Set optional Tweet ID. Optionality allows
     * responsibility of validation elsewhere.
     *
     * @param int|null $id
     * @return \App\Twitter\Models\ReachResult
     */
    public function setTweetId(?int $id): ReachResult
    {
        $this->tweetId = $id;

        return $this;
    }

    /**
     * Get author in the form of a User object.
     *
     * @return \App\Twitter\Models\User
     */
    public function getAuthor(): User
    {
        return $this->tweet->user;
    }

    /**
     * Returns whether we have a valid Tweet ID.
     *
     * @return bool
     */
    public function tweetIdIsValid(): bool
    {
        return $this->tweetId !== null;
    }

    /**
     * Return result data as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'reach'            => $this->getReach(),
            'humanizedReach'   => $this->getHumanizedReachMetric(),
            'reachDescription' => $this->getReachDescription(),
            'hasRetweets'      => $this->hasRetweets(),
            'retweetersCount'  => $this->getRetweetersCount(),
            'tweet'            => $this->tweet->toArray(),
        ];
    }

    /**
     * Get reach metric.
     *
     * @return int
     */
    public function getReach(): int
    {
        return $this->reach;
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
     * Get reach description: one sentence with humanized
     * reach number and the raw reach integer.
     *
     * @return string
     */
    public function getReachDescription(): string
    {
        return trans('wizard.result', [
            'humanized' => $this->getHumanizedReachMetric(),
            'number'    => $this->getReach(),
        ]);
    }

    /**
     * Return whether we have retweets.
     *
     * @return bool
     */
    public function hasRetweets(): bool
    {
        return ($this->tweet->retweet_count ?? 0) > 0;
    }

    /**
     * Get count of retweeters
     *
     * @return int
     */
    public function getRetweetersCount(): int
    {
        return $this->retweetersCount;
    }

    /**
     * Get retweeters collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRetweeters(): Collection
    {
        return $this->retweeters;
    }

    /**
     * Get original Tweet.
     *
     * @return \App\Twitter\Models\Tweet
     */
    public function getTweet(): Tweet
    {
        return $this->tweet;
    }

    /**
     * Set optional Tweet ID. Optionality allows
     * responsibility of validation elsewhere.
     *
     * @param Tweet $tweet
     * @return \App\Twitter\Models\ReachResult
     */
    public function setTweet(Tweet $tweet): ReachResult
    {
        $this->tweet = $tweet;

        return $this;
    }
}
