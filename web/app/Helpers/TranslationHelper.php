<?php

namespace App\Helpers;

use Illuminate\Support\Facades\View;

class TranslationHelper
{
    /**
     * Translate text based on current language preference
     *
     * @param string $english English text
     * @param string $tagalog Tagalog text
     * @return string
     */
    public static function translate($english, $tagalog)
    {
        // Get the current language preference from the view
        $useTagalog = View::shared('useTagalog') ?? false;
        
        return $useTagalog ? $tagalog : $english;
    }
}