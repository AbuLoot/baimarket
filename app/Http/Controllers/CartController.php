<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;
use App\Country;
use App\Order;

use Auth;
use Mail;

class CartController extends Controller
{
    public function cart(Request $request)
    {
        $countries = Country::all();

        if ($request->session()->has('items')) {

            $items = $request->session()->get('items');
            $data_id = collect($items['products_id']);
            $products = Product::whereIn('id', $data_id->keys())->get();
        }
        else {
            $products = collect();
        }

        return view('cart', compact('products', 'countries'));
    }

    public function addToCart(Request $request, $lang, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->session()->has('items')) {

            $items = $request->session()->get('items');
            $quantity = (isset($request->quantity)) ? $request->quantity : 1;

            $items['products_id'][$id] = [
                'id' => $id, 'quantity' => $quantity, 'slug' => $product->slug, 'title' => $product->title, 'img_path' => $product->path.'/'.$product->image, 'price' => $product->price,
            ];

            $count = count($items['products_id']);

            $request->session()->put('items', $items);

            return response()->json([
                'alert' => 'Товар обновлен', 'countItems' => $count, 'quantity' => $request->quantity, 'slug' => $product->slug, 'title' => $product->title, 'img_path' => $product->path.'/'.$product->image, 'price' => $product->price,
            ]);
        }

        $items = [];
        $items['products_id'][$id] = [
            'id' => $id, 'quantity' => 1, 'slug' => $product->slug, 'title' => $product->title, 'img_path' => $product->path.'/'.$product->image, 'price' => $product->price,
        ];

        $request->session()->put('items', $items);

        return response()->json([
            'alert' => 'Товар обновлен', 'countItems' => 1, 'slug' => $product->slug, 'title' => $product->title, 'img_path' => $product->path.'/'.$product->image, 'price' => $product->price,
        ]);
    }

    public function removeFromCart(Request $request, $lang, $id)
    {
        $items = $request->session()->get('items');
        $count = count($items['products_id']);

        if ($count == 1) {
            $count = 0;
            $request->session()->forget('items');
        }
        else {
            unset($items['products_id'][$id]);
            $count = $count - 1;
            $request->session()->put('items', $items);
        }

        return response()->json(['countItems' => $count]);
    }

    public function clearCart(Request $request)
    {
        $request->session()->forget('items');

        return redirect('/');
    }

    public function storeOrder(Request $request)
    {
        $this->validate($request, [
            'surname' => 'required|min:2|max:255',
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|min:5',
            'city_id' => 'numeric',
            'address' => 'required',
        ]);

        $items = $request->session()->get('items');
        $data_id = collect($items['products_id']);
        $products = Product::whereIn('id', $data_id->keys())->get();

        $sumCountProducts = 0;
        $sumPriceProducts = 0;

        foreach ($products as $product) {
            $sumCountProducts += $request->count[$product->id];
            $sumPriceProducts += $request->count[$product->id] * $product->price;
        }

        $order = new Order;
        $order->user_id = ((Auth::check())) ? Auth::id() : 0;
        $order->name = $request->name;
        $order->phone = $request->phone;
        $order->email = $request->email;
        $order->company_name = $request->company_name;
        $order->data_1 = $request->notes;
        $order->data_2 = $request->postcode;
        $order->data_3 = '';
        $order->legal_address = '';
        $order->city_id = ($request->city_id) ? $request->city_id : 0;
        $order->address = $request->address;
        $order->count = serialize($request->count);
        $order->price = $products->sum('price');
        $order->amount = $sumPriceProducts;
        $order->delivery = $request->get;
        $order->payment_type = $request->pay;
        $order->save();

        $order->products()->attach($data_id->keys());

        $name = $request->name;

        // Email subject
        $subject = "Kezer - Новая заявка от $request->name";

        $headers = "From: info@kezer.kz \r\n" .
                   "MIME-Version: 1.0" . "\r\n" . 
                   "Content-type: text/html; charset=UTF-8" . "\r\n";

        $content = view('partials.mail-new-order', ['order' => $order])->render();

        try {
            mail('issayev.adilet@gmail.com', $subject, $content, $headers);

            $status = 'alert-success';
            $message = 'Ваш заказ принят!';

            // Mail::send('vendor.mail.html.layout', ['order' => $order], function($message) use ($name) {
            //     $message->to(['abdulaziz.abishov@gmail.com', 'issayev.adilet@gmail.com'], 'Kezer')->subject('Kezer - Новый заказ от '.$name);
            //     $message->from('electron.servant@gmail.com', 'Electron Servant');
            // });

            $response = view('partials.mail-client-order', ['order' => $order])->render();

            mail($order->email, 'Kezer - ваш заказ: '.$order->id, $response, $headers);

        } catch (Exception $e) {

            $status = 'alert-danger';
            $message = 'Произошла ошибка: '.$e->getMessage();
        }

        $request->session()->forget('items');

        return redirect()->back()->with([
            'info' => $message
        ]);
    }

    public function destroy(Request $request, $lang, $id)
    {
        $items = $request->session()->get('items');

        if (count($items['products_id']) == 1) {
            $request->session()->forget('items');
        }
        else {
            unset($items['products_id'][$id]);
            $request->session()->put('items', $items);
        }

        return redirect('cart');
    }
}