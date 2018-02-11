<?php

/* @var $factory Illuminate\Database\Eloquent\Factory */

use App\Twitter\Models\Tweet;
use Carbon\Carbon;
use Faker\Generator as Faker;

// Default Tweet
$factory->define(Tweet::class, function (Faker $faker) {
    $retweeted = $faker->boolean;
    $randomDate = $faker->dateTimeBetween('-30 days', '+30 days');

    return [
        'id'                            => $faker->numberBetween(10000000000000000, 900000000000000000),
        'text'                          => $faker->paragraph(),
        'entities'                      => [],
        'source'                        => $faker->url,
        'lang'                          => $faker->countryCode,
        'retweeted'                     => $retweeted,
        'retweet_count'                 => $retweeted ? $faker->numberBetween(1, 1000000) : 0,
        'favorited'                     => $faker->boolean,
        'possibly_sensitive'            => $faker->boolean,
        'possibly_sensitive_appealable' => $faker->boolean,
        'is_quote_status'               => $faker->boolean,
        'created_at'                    => Carbon::createFromTimestamp($randomDate->getTimestamp()),
    ];
});

// Deterministic for tests
$factory->state(Tweet::class, 'deterministic', function () {
    return [
        'retweeted'          => true,
        'favorited'          => true,
        'possibly_sensitive' => true,
        'retweet_count'      => 1,
    ];
});
