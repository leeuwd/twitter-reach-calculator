<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Twitter\Models\ReachResult;
use App\Twitter\Models\Tweet;
use App\Twitter\Models\User;

class ReachResultTest extends TestCase
{
    /**
     * Test construction.
     *
     * @covers ReachResult::__construct()
     * @covers ReachResult::init()
     * @return void
     * @throws \Exception
     */
    public function testConstruction(): void
    {
        $result = new ReachResult;

        // We should have a an empty collection
        $this->assertSame(0, $result->getRetweeters()->count());
    }

    /**
     * Test adding a retweeter to result.
     *
     * @covers ReachResult::addRetweeter()
     * @return void
     * @throws \Exception
     */
    public function testAddRetweeter(): void
    {
        $result = new ReachResult;
        $user = factory(User::class)->states(['deterministic'])->make();
        $result->addRetweeter($user);

        // Assertions
        $this->assertSame($user, $result->getRetweeters()->pop());
        $this->assertSame(1, $result->getRetweetersCount());
        $this->assertSame(1, $result->getReach());
    }

    /**
     * Test setting the original Tweet.
     *
     * @covers ReachResult::setTweet()
     * @return void
     * @throws \Exception
     */
    public function testSetTweet(): void
    {
        $result = new ReachResult;
        $tweet = factory(Tweet::class)->states(['deterministic'])->make();
        $result->setTweet($tweet);

        // Assertions
        $this->assertSame($tweet, $result->getTweet());
    }

    /**
     * Test getting original Tweet author.
     *
     * @covers ReachResult::getAuthor()
     * @return void
     * @throws \Exception
     */
    public function testGetAuthor(): void
    {
        $result = new ReachResult;
        $user = factory(User::class)->make();
        $tweet = factory(Tweet::class)->states(['deterministic'])->make();
        $tweet->user = $user;
        $result->setTweet($tweet);

        // Assertions
        $this->assertSame($user, $result->getAuthor());
    }
}
