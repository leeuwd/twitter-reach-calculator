<?php
declare(strict_types=1);

namespace App\Twitter\Transformers;

use Log;

abstract class Transformer
{
    /**
     * Indicates a callback function for parsing input
     * data. E.g. '@created|' . self::class . '::parseDate'
     * parses the 'created' field with a static function parseDate().
     */
    public const CALLBACK_INDICATOR_CHAR = '@';

    /**
     * Field name - callback delimiter.
     */
    public const DELIMITER_CHAR = '|';

    /**
     * Mapping.
     *
     * @var array
     */
    protected static $mapping = [];

    /**
     * Transform API input payload into internal format.
     *
     * @param \stdClass|array $rawData
     * @return array[]
     */
    public static function transform($rawData): array
    {
        $result = [];

        // Type cast to array, since array handling is easier
        if (! \is_array($rawData)) {
            $rawData = json_decode(json_encode($rawData), true);
        }

        Log::debug('Raw data to transform', $rawData);

        // Loop through mapping and parse/set
        foreach (static::$mapping as $inputField => $internalField) {
            $internalFieldName = $internalField;
            $internalFieldValue = $rawData[$inputField];
            $useCallback = \strpos($internalField, self::CALLBACK_INDICATOR_CHAR) === 0;

            // Parse via callback function
            if ($useCallback) {
                $internalFieldData = \explode(self::DELIMITER_CHAR, \ltrim($internalField, self::CALLBACK_INDICATOR_CHAR));

                $internalFieldName = $internalFieldData[0];
                $internalFieldValue = \call_user_func($internalFieldData[1], $rawData[$inputField]);
            }

            $result[$internalFieldName] = $internalFieldValue;
        }

        return $result;
    }
}
