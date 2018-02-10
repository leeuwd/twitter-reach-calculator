<?php
declare(strict_types=1);

namespace App\Twitter\Transformers;

use App\Twitter\Mappings\UserMapping;

class UserTransformer extends Transformer
{
    /**
     * Mapping.
     *
     * @var array
     */
    protected static $mapping = UserMapping::MAPPING;
}
