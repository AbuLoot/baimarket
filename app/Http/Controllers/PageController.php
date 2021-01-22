<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use View;

use App\Page;
use App\Mode;
use App\Slide;
use App\Article;
use App\Product;
use App\ProductLang;
use App\Currency;
use App\Category;

class PageController extends Controller
{
    public function index($lang)
    {
        app()->setLocale($lang);
        $lang = app()->getLocale();
        $slide_items = Slide::where('status', 1)->where('lang', $lang)->take(6)->get();
        $page = Page::where('slug', '/')->where('lang', $lang)->first();
        $products_lang = ProductLang::where('lang', $lang)->get();
        $currency = Currency::where('lang', (($lang == 'ru') ? 'kz' : $lang))->first();

        return view('index', ['page' => $page, 'slide_items' => $slide_items, 'products_lang' => $products_lang, 'currency' => $currency]);
    }

    public function catalog($lang, $condition = '')
    {
        $page = Page::where('slug', 'catalog')->where('lang', $lang)->first();
        $products_lang = ProductLang::where('lang', $lang)->paginate(15);
        $currency = Currency::where('lang', (($lang == 'ru') ? 'kz' : $lang))->first();

        return view('listings', ['page' => $page, 'products_lang' => $products_lang, 'currency' => $currency]);
    }

    public function page($lang, $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('pages.page')->with('page', $page);
    }

    public function contacts()
    {
        $page = Page::where('slug', 'contacts')->firstOrFail();

        return view('pages.contacts')->with('page', $page);
    }
}
