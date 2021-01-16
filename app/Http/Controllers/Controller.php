<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;

use App\Page;
use App\Region;
use App\Company;
use App\Section;
use App\Category;
use App\Language;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        app()->setLocale(\Request::segment(1));

        $lang = app()->getLocale();
        $section = Section::all();
        $section_codes = Section::whereIn('slug', ['header-code', 'footer-code'])->get();
        $companies = Company::where('status', 2)->orderBy('sort_id')->get();
        $pages = Page::where('status', 1)->whereNotIn('slug', ['/'])->orderBy('sort_id')->get()->toTree();
        $categories = Category::where('status', '<>', 0)->orderBy('sort_id')->get()->toTree();

        View::share([
            'lang' => $lang,
            'pages' => $pages,
            'companies' => $companies,
            'section' => $section,
            'section_codes' => $section_codes,
            'categories' => $categories,
        ]);
    }
}
