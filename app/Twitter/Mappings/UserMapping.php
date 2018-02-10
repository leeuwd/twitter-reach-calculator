<?php
declare(strict_types=1);

namespace App\Twitter\Mappings;

class UserMapping extends Mapping
{
    /**
     * User fields -> internal field names mapping.
     *
     * @var array
     */
    public const MAPPING = [
        'id'                      => 'id',
        'name'                    => 'name',
        'screen_name'             => 'screen_name',
        'location'                => 'location',
        'description'             => 'description',
        'url'                     => 'url',
        'followers_count'         => 'followers_count',
        'friends_count'           => 'friends_count',
        'listed_count'            => 'listed_count',
        'favourites_count'        => 'favourites_count',
        'time_zone'               => 'time_zone',
        'geo_enabled'             => 'geo_enabled',
        'verified'                => 'verified',
        'statuses_count'          => 'statuses_count',
        'lang'                    => 'lang',
        'profile_image_url_https' => 'profile_image_url_https',
        'created_at'              => '@created_at|' . self::class . '::parseDate',
    ];
}
