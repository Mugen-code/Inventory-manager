<?php

namespace App\Controllers;

class Language extends BaseController
{
    public function setLanguage($locale)
    {
        $supportedLocales = ['en', 'sl'];
        
        if (in_array($locale, $supportedLocales)) {
            session()->set('locale', $locale);
        }
        
        return redirect()->to($_SERVER['HTTP_REFERER'] ?? base_url());
    }
}