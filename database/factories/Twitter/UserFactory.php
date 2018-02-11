<?php

/* @var $factory Illuminate\Database\Eloquent\Factory */

use App\Twitter\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

// Default User
$factory->define(User::class, function (Faker $faker) {
    $randomDate = $faker->dateTimeBetween('-30 days', '+30 days');

    return [
        'id'                      => $faker->numberBetween(10000000000000000, 900000000000000000),
        'name'                    => $faker->name,
        'screen_name'             => $faker->firstName,
        'location'                => $faker->city . ' ' . $faker->country,
        'description'             => $faker->sentence,
        'url'                     => $faker->url,
        'lang'                    => $faker->countryCode,
        'profile_image_url_https' => $faker->url,
        'followers_count'         => $faker->numberBetween(0, 1000000),
        'friends_count'           => $faker->numberBetween(0, 1000000),
        'listed_count'            => $faker->numberBetween(0, 1000000),
        'favourites_count'        => $faker->numberBetween(0, 1000000),
        'statuses_count'          => $faker->numberBetween(0, 1000000),
        'time_zone'               => $faker->timezone,
        'geo_enabled'             => $faker->boolean,
        'verified'                => $faker->boolean,
        'created_at'              => Carbon::createFromTimestamp($randomDate->getTimestamp()),
    ];
});

// Leecher
$factory->state(User::class, 'leecher', function () {
    return [
        'statuses_count' => 0,
    ];
});

// Deterministic for tests
$factory->state(User::class, 'deterministic', function () {
    return [
        'followers_count'  => 1,
        'friends_count'    => 1,
        'listed_count'     => 1,
        'favourites_count' => 1,
        'statuses_count'   => 1,
    ];
});

