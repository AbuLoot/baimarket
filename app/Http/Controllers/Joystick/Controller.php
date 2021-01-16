<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Language;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        app()->setLocale(\Request::segment(1));

        $lang = app()->getLocale();
        $languages = Language::orderBy('sort_id')->get();

        view()->share([
            'lang' => $lang,
            'languages' => $languages
        ]);
    }
}
