<?php

namespace App\Helpers;

class EscapeHelper
{
    /**
     * Unescape string input (HTML entities back to original).
     *
     * @param  string  $value
     * @return string
     */
    public static function unescapeInput(string $value): string
    {
        // 1. Decode hex entities: &#x27;
        $value = preg_replace_callback('/&#x([0-9a-fA-F]+);/', function ($match) {
            return chr(hexdec($match[1]));
        }, $value);

        // 2. Decode decimal entities: &#39;
        $value = preg_replace_callback('/&#(\d+);/', function ($match) {
            return chr((int)$match[1]);
        }, $value);

        // 3. Decode named HTML entities (e.g., &amp; &quot;)
        $namedEntities = [
            '&quot;' => '"',
            '&amp;'  => '&',
            '&lt;'   => '<',
            '&gt;'   => '>',
            '&apos;' => "'",
        ];

        return strtr($value, $namedEntities);
    }

    public static function unescapeArray(array $values): array
    {
        return array_map([self::class, 'unescapeInput'], $values);
    }
}
