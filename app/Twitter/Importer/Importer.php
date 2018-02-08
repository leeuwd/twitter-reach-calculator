<?php
declare(strict_types=1);

namespace App\Twitter\Importer;

use App\Twitter\Models\Tweet;
use App\Twitter\Transformers\TweetTransformer;
use Twitter as TwitterApi;

class Importer
{
    /**
     * Get single Tweet object.
     *
     * @param int $tweetId
     * @return Tweet
     */
    public static function getTweet(int $tweetId): Tweet
    {
        // Fetch raw data via API
        $rawObject = TwitterApi::getTweet($tweetId);

        // Hydrate Tweet model
        return (new Tweet)->forceFill(TweetTransformer::transform($rawObject));
    }
}
