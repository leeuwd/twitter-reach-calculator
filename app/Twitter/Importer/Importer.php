<?php
declare(strict_types=1);

namespace App\Twitter\Importer;

use App\Twitter\Models\ReachResult;
use App\Twitter\Models\Tweet;
use App\Twitter\Models\User;
use App\Twitter\Transformers\TweetTransformer;
use App\Twitter\Transformers\UserTransformer;
use Twitter as TwitterApi;

class Importer
{
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
     */
    public static function computeReach(string $tweetUrl): ReachResult
    {
        $result = new ReachResult();
        $result->tweetUrl = $tweetUrl;
        $result->tweetUrlValid = self::isValidUrl($tweetUrl);
        $result->tweetId = $result->tweetUrlValid ? self::getTweetIdFromTweetUrl($tweetUrl) : null;
        $result->tweetIdValid = $result->tweetId !== null;

        // Performance improvement, stop early
        if (! ($result->tweetUrlValid && $result->tweetIdValid)) {
            return $result;
        }

        // Set original Tweet
        $result->tweet = self::getTweet($result->tweetId);
        $result->author = $result->tweet->user ?? null;
        $result->hasRetweets = ($result->tweet->retweet_count ?? 0) > 0;

        // Performance improvement, stop early
        if (! $result->hasRetweets) {
            return $result;
        }

        // Add collection of retweeters, which
        // computes the reach while doing so
        self::addRetweetersCollectionToResult($result);

        // Return object
        return $result;
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
     */
    public static function getTweetIdFromTweetUrl(string $tweetUrl): ?int
    {
        $parts = explode('/', $tweetUrl);
        $id = end($parts);

        // Quick and dirty validation
        if (! is_numeric($id)) {
            return null;
        }

        return (int) $id;
    }

    /**
     * Get single Tweet object.
     *
     * @param int $tweetId
     * @return Tweet
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
     */
    public static function addRetweetersCollectionToResult(ReachResult $result): void
    {
        /* @var $tweetersData array[] */
        /* @var $usersData array[] */

        $retweetersParameters = [
            'id'     => $result->tweetId,
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

        // CLI progress bar
        if (static::$useProgressBar) {
            $bar = static::$commandHandler::getProgressBar(count($tweeterIds) + 1);
            $bar->setMessage('Fetching retweeters…');
        }

        // Fetch user data via API
        $usersData = TwitterApi::getUsersLookup($usersParameters);

        // CLI progress bar
        if (static::$useProgressBar) {
            $bar->setMessage('Fetching user data…');
            $bar->advance();
        }

        // Loop through user
        foreach ($usersData as $userData) {

            // Hydrate user and and to result's model collection
            $user = (new User)->forceFill(UserTransformer::transform($userData));
            $result->addRetweeter($user);

            // CLI progress bar
            if (static::$useProgressBar) {
                $bar->advance();
            }
        }

        // CLI progress bar
        if (static::$useProgressBar) {
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
}
