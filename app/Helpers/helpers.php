<?php

use Illuminate\Support\Str;

if (!function_exists('sanitizeString')) {
    /**
     * Sanitize a string by replacing special characters with a replacement.
     *
     * @param string $input
     * @param string $replacement
     * @return string
     */
    function sanitizeString(string $input, string $replacement = ' '): string
    {
        // Characters to replace: & [ ] - / . , ( )
        $pattern = '/[&\[\]\-\/\.,\(\):;"!$%\^*\|\'\?]/';

        // Replace with specified character
        $cleaned = preg_replace($pattern, $replacement, $input);

        // Remove extra spaces
        $cleaned = Str::squish($cleaned);

        return $cleaned;
    }
}