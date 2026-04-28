<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (! in_array($locale, ['en', 'bn'])) {
            abort(400);
        }

        Session::put('locale', $locale);

        return redirect()->back()->withHeaders([
            'Cache-Control' => 'no-cache, no-store',
        ]);
    }
}
