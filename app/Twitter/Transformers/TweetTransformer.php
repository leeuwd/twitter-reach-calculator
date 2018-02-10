<?php
declare(strict_types=1);

namespace App\Twitter\Transformers;

use App\Twitter\Mappings\TweetMapping;

class TweetTransformer extends Transformer
{
    /**
     * Mapping.
     *
     * @var array
     */
    protected static $mapping = TweetMapping::MAPPING;
}
