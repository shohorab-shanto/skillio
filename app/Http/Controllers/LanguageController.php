<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $lang = $request->input('lang', 'en');
        if (!in_array($lang, ['en', 'hr', 'sr', 'sl', 'mk'])) {
            $lang = 'en';
        }
        session(['locale' => $lang]);
        app()->setLocale($lang);
        return Redirect::back();
    }
}
