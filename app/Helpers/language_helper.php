<?php

if (!function_exists('lang')) {
    function lang(string $key): string
    {
        $locale = session()->get('locale') ?? 'en';
        $langFile = APPPATH . 'Language/' . $locale . '/app.php';
        
        if (file_exists($langFile)) {
            $translations = require $langFile;
            return $translations[$key] ?? $key;
        }
        
        return $key;
    }
}