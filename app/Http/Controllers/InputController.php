<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use URL;
use Session;

use App\App;
use App\Region;
use App\Project;
use App\Product;
use App\ProductLang;
use App\Category;

class InputController extends Controller
{
    public function search(Request $request)
    {
        $text = trim(strip_tags($request->text));

	    // $products = Product::where('status', '<>', 0)
     //        ->where('region_id', $region_id)
     //        ->whereHas('products_lang', function($query) use ($text) {
     //            $query->where('products_lang.title', 'LIKE', "%{$text}%")
     //                ->orWhere('products_lang.description', 'LIKE', "%{$text}%")
     //                ->orWhere('products_lang.characteristic', 'LIKE', "%{$text}%");
     //        })
     //        // ->with('products_lang')
     //        ->paginate(30);

        $products_lang = ProductLang::search($text)
            // ->with('products')
            ->paginate(30);

        $products_lang->appends([
            'text' => $text
        ]);

        return view('found', compact('text', 'products_lang'));
    }

    public function searchAjax(Request $request)
    {
        $text = trim(strip_tags($request->text));
        $products_lang = ProductLang::search($text)->take(20)->get();
        $array = [];

        foreach ($products_lang as $key => $product_lang) {
            $array[$key]['id'] = $product_lang->product_id;
            $array[$key]['path'] = $product_lang->product->path;
            $array[$key]['image'] = $product_lang->product->image;
            $array[$key]['barcode'] = $product_lang->product->barcode;
            $array[$key]['title'] = $product_lang->title;
            $array[$key]['lang'] = $product_lang->lang;
        }

        return response()->json($array);
    }

    public function setRegion(Request $request, $lang)
    {
        $city = trim(strip_tags($request->city[0]));
        $city = Region::where('slug', $city)->orWhere('title', $city)->first();

        $request->session()->put('region', $city->slug);

        return response()->json($city);
    }

    public function filterProducts(Request $request)
    {
        $from = ($request->price_from) ? (int) $request->price_from : 0;
        $to = ($request->price_to) ? (int) $request->price_to : 9999999999;

        $products = Product::where('status', 1)->whereBetween('price', [$request->from, $request->to])->paginate(27);

        return redirect()->back()->with([
            'alert' => $status,
            'products' => $products
        ]);
    }

    public function sendApp(Request $request, $lang)
    {
        $this->validate($request, [
            'name' => 'required|min:2|max:60',
            'phone' => 'required|min:5|max:60',
        ]);

        $url = explode('/', URL::previous());
        $id = end($url);
        $product = Product::findOrFail($request->id);

        if ('p-'.$request->id === $id AND $request->type === 'app'AND $request->owner == $product->company_id) {

            $app = new App;
            $app->name = $request->name;
            $app->email = ($request->email) ? $request->email : NULL;
            $app->phone = $request->phone;
            $app->message = $request->time;
            $app->company_id = $product->company_id;
            $app->file = $product->id;
            $app->save();
        }

        // Email subject
        $subject = "5 Second - Новая заявка от $request->name";

        // Email content
        $content = "<h2>5 Second</h2>";
        $content .= "<b>Имя: $request->name</b><br>";
        $content .= "<b>Номер: $request->phone</b><br>";
        $content .= "<b>Email: $request->email</b><br>";
        $content .= "<b>Время бронирования: $request->time</b><br>";
        $content .= "<b>Объявление: $product->title</b><br>";
        $content .= "<b>Дата: " . date('Y-m-d') . "</b><br>";
        $content .= "<b>Время: " . date('G:i') . "</b>";

        $headers = "From: info@5second.kz \r\n" .
                   "MIME-Version: 1.0" . "\r\n" . 
                   "Content-type: text/html; charset=UTF-8" . "\r\n";

        // Send the email
        if (mail('issayev.adilet@gmail.com', $subject, $content, $headers)) {
            $status = 'alert-success';
            $message = 'Ваша заявка принята. Спасибо!';
        }
        else {
            $status = 'alert-danger';
            $message = 'Произошла ошибка.';
        }

        // dd($status, $message);
        return redirect()->back()->with([
            'status' => $status,
            'message' => $message
        ]);
    }
}