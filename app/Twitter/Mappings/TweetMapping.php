<?php
declare(strict_types=1);

namespace App\Twitter\Mappings;

use App\Twitter\Models\User;
use App\Twitter\Transformers\UserTransformer;

class TweetMapping extends Mapping
{
    /**
     * Tweet fields -> internal field names mapping.
     *
     * @var array
     */
    public const MAPPING = [
        'id'                            => 'id',
        'text'                          => 'text',
        'entities'                      => 'entities',
        'source'                        => 'source',
        'lang'                          => 'lang',
        'retweeted'                     => 'retweeted',
        'favorited'                     => 'favorited',
        'possibly_sensitive'            => 'possibly_sensitive',
        'possibly_sensitive_appealable' => 'possibly_sensitive_appealable',
        'is_quote_status'               => 'is_quote_status',
        'user'                          => '@user|' . self::class . '::parseUser',
        'created_at'                    => '@created_at|' . self::class . '::parseDate',
    ];

    /**
     * Extract user object out of raw payload.
     *
     * @param array|null $input
     * @return \App\Twitter\Models\User|null
     */
    public static function parseUser(?array $input = null): User
    {
        // Nothing there
        if (empty($input)) {
            return null;
        }

        // Hydrate user model
        return (new User)->forceFill(UserTransformer::transform($input));
    }
}
