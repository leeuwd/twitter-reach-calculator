<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Twitter\Models\Tweet;
use App\Twitter\Models\User;

class TweetTest extends TestCase
{
    /**
     * A proof-of-concept unit tests to show my
     * understanding of unit tests.
     *
     * @return void
     * @throws \Exception
     */
    public function testModelFactory(): void
    {
        /* @var $tweets \Illuminate\Database\Eloquent\Collection */

        // Create collection
        $tweets = factory(Tweet::class, 5)->states(['deterministic'])->make()->each(function ($tweet) {
            $tweet->user = factory(User::class)->make();
        });

        // Since we use deterministic state, all Tweets have a retweet count of 1
        $totalRetweetCount = $tweets->sum('retweet_count');

        // Assertions
        $this->assertSame(5, $totalRetweetCount);
        $this->assertSame(1, $tweets->pop()->retweet_count);
        $this->assertInstanceOf(User::class, $tweets->pop()->user);
    }
}
