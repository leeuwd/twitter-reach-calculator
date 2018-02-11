<?php
declare(strict_types=1);

namespace App\Twitter\Mappings;

use Throwable;
use Carbon\Carbon;

abstract class Mapping
{
    /**
     * Parse Tweet date.
     *
     * @param string|null $input
     * @return Carbon|null
     */
    public static function parseDate(?string $input = null): ?Carbon
    {
        // Null
        if ($input === null) {
            return null;
        }

        try {
            return Carbon::parse($input);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
