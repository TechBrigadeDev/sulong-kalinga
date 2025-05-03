<?php

namespace App\Helpers;

class StringHelper
{
    public static function formatArea($string)
    {
        // Replace underscores with spaces and capitalize each word
        $formattedString = ucwords(str_replace('_', ' ', $string));

        // Capitalize specific words
        $formattedString = str_replace('Meal', 'MEAL', $formattedString);

        return $formattedString;
    }
}