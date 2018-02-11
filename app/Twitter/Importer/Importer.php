<?php
declare(strict_types=1);

namespace App\Twitter\Importer;

use App\Twitter\Models\ReachResult;
use App\Twitter\Models\Tweet;
use App\Twitter\Models\User;
use App\Twitter\Transformers\TweetTransformer;
use App\Twitter\Transformers\UserTransformer;
use Cache;
use Twitter as TwitterApi;

class Importer
{
    /**
     * Cache lifetime.
     *
     * @var int
     */
    public const CACHE_LIFETIME_MINUTES = 120;

    /**
     * Cache prefix to reduce likelihood
     * of hash collisions.
     *
     * @var int
     */
    public const CACHE_PREFIX = self::class;

    /**
     * Format of results returned by API.
     *
     * @var string object|json|array
     */
    private const TWITTER_API_DEFAULT_FORMAT = 'array';

    /**
     * Page size. Max 100. Please be aware by design the value of count is best
     * thought of as a limit to the number of tweets to return because suspended
     * or deleted content is removed after the count has been applied.
     *
     * @var int
     */
    private const TWITTER_API_PAGE_SIZE = 100;

    /**
     * Flag that toggles progress bar output.
     *
     * @var bool
     */
    private static $useProgressBar = false;

    /**
     * Flag that toggles progress bar output.
     *
     * @var \App\Twitter\Importer\Command|null
     */
    private static $commandHandler;

    /**
     * Compute reach. Result object is returned.
     *
     * @param string $tweetUrl
     * @return ReachResult
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public static function computeReach(string $tweetUrl): ReachResult
    {
        $key = md5(self::CACHE_PREFIX . $tweetUrl);

        // Closure called when result not in cache
        return Cache::remember($key, self::CACHE_LIFETIME_MINUTES, function () use ($tweetUrl) {
            return self::computeReachResults($tweetUrl);
        });
    }

    /**
     * Compute reach. Result object is returned.
     *
     * @param string $tweetUrl
     * @return ReachResult
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    protected static function computeReachResults(string $tweetUrl): ReachResult
    {
        // Parse input to get Tweet ID
        $tweetId = self::isValidUrl($tweetUrl) ? self::getTweetIdFromTweetUrl($tweetUrl) : null;

        // Set initial data
        $result = (new ReachResult())
            ->setTweetUrl($tweetUrl)
            ->setTweetId($tweetId);

        // Performance improvement, stop early
        if (! $result->tweetIdIsValid()) {
            return $result;
        }

        // Set original Tweet
        $result->setTweet(self::getTweet($tweetId));

        // Performance improvement, stop early
        if (! $result->hasRetweets()) {
            return $result;
        }

        // Add collection of retweeters, which
        // computes the reach while doing so
        self::addRetweetersCollectionToResult($result);

        // Return object
        return $result;
    }

    /**
     * Validate URL argument.
     *
     * @param string|null $url
     * @return bool
     */
    protected static function isValidUrl(?string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Extract Tweet ID from Tweet URL.
     *
     * @param string $tweetUrl
     * @return int|null
     * @throws \InvalidArgumentException
     */
    public static function getTweetIdFromTweetUrl(string $tweetUrl): ?int
    {
        $parts = explode('/', $tweetUrl);
        $id = end($parts);

        // Quick and dirty validation; expected input is 64-bit unsigned integer
        // @see https://developer.twitter.com/en/docs/basics/twitter-ids
        if (! is_numeric($id)) {
            throw new \InvalidArgumentException('The Tweet URL does not contain a valid status ID.');
        }

        return (int) $id;
    }

    /**
     * Get single Tweet object.
     *
     * @param int $tweetId
     * @return Tweet
     * @throws \RuntimeException
     */
    public static function getTweet(int $tweetId): Tweet
    {
        // Fetch raw data via API
        $rawObject = TwitterApi::getTweet($tweetId, ['format' => self::TWITTER_API_DEFAULT_FORMAT]);

        // Hydrate Tweet model
        return (new Tweet)->forceFill(TweetTransformer::transform($rawObject));
    }

    /**
     * Construct collection of retweeters and
     * add those to the result object. While
     * doing so, the result object computes
     * the reach.
     *
     * @param ReachResult $result
     * @see https://developer.twitter.com/en/docs/tweets/post-and-engage/api-reference/get-statuses-retweeters-ids
     * @return void
     * @throws \RuntimeException
     */
    public static function addRetweetersCollectionToResult(ReachResult $result): void
    {
        /* @var $tweetersData array[] */
        /* @var $usersData array[] */

        $retweetersParameters = [
            'id'     => $result->getTweetId(),
            'format' => self::TWITTER_API_DEFAULT_FORMAT,
            'count'  => self::getPageSize(),
        ];

        // By design, the Twitter API 1.1 only returns max 100 user IDs who retweeted a Tweet! If
        // this limitation wasn't there, we could use a cursor to loop through result pages,
        // see https://developer.twitter.com/en/docs/basics/cursoring. For now, we'll act like
        // 100 is the max there is.
        $tweetersData = TwitterApi::getRters($retweetersParameters);
        $tweeterIds = $tweetersData['ids'] ?? [];

        // Since we have at max. 100 retweeters, we can fetch all user data at once. If this
        // was not possible, we could use array_chunk() to split the job up in batches.
        $usersParameters = [
            'user_id' => implode(',', $tweeterIds),
            'format'  => self::TWITTER_API_DEFAULT_FORMAT,
            'count'   => self::getPageSize(),
        ];

        // Create progress bar (when invoked via CLI)
        $bar = static::$useProgressBar ? static::$commandHandler::getProgressBar(\count($tweeterIds) + 1) : false;

        // CLI progress bar
        if ($bar) {
            $bar->setMessage('Fetching retweeters…');
        }

        // Fetch user data via API
        $usersData = TwitterApi::getUsersLookup($usersParameters);

        // Progress update
        if ($bar) {
            $bar->setMessage('Fetching user data…');
            $bar->advance();
        }

        // Loop through user
        foreach ($usersData as $userData) {

            // Hydrate user and and to result's model collection
            $user = (new User)->forceFill(UserTransformer::transform($userData));
            $result->addRetweeter($user);

            // CLI progress bar
            if ($bar) {
                $bar->advance();
            }
        }

        // CLI progress bar
        if ($bar) {
            $bar->clear();
        }
    }

    /**
     * Return page size.
     *
     * @return int
     */
    protected static function getPageSize(): int
    {
        return min(self::TWITTER_API_PAGE_SIZE, 100);
    }

    /**
     * Toggle progress bar for the operations that take time.
     *
     * @param bool $flag
     * @return void
     */
    public static function useProgressBar(bool $flag): void
    {
        static::$useProgressBar = $flag;
    }

    /**
     * Toggle progress bar for the operations that take time.
     *
     * @param \App\Twitter\Importer\Command $handler
     * @return void
     */
    public static function setCommandHandler(Command $handler): void
    {
        static::$commandHandler = $handler;
    }
}
