<?php
declare(strict_types=1);

namespace App\Twitter\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Twitter\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $screen_name
 * @property string $location
 * @property string $description
 * @property string $url
 * @property string $lang
 * @property string $profile_image_url_https
 * @property int $followers_count
 * @property int $friends_count
 * @property int $listed_count
 * @property int $favourites_count
 * @property int $statuses_count
 * @property string $time_zone
 * @property bool $geo_enabled
 * @property bool $verified
 * @property \Carbon\Carbon|null $created_at
 */
class User extends Model
{
}
