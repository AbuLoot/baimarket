<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;
use App\Region;
use App\Order;

use Auth;
use Mail;

class CartController extends Controller
{
    public function cart(Request $request)
    {
        if ($request->session()->has('items')) {

            $items = $request->session()->get('items');
            $data_id = collect($items['products_id']);
            $products = Product::whereIn('id', $data_id->keys())->get();
        }
        else {
            $products = collect();
        }

        return view('cart', compact('products'));
    }

    public function checkout(Request $request)
    {
        $regions = Region::all();

        $items = $request->session()->get('items');
        $data_id = collect($items['products_id']);
        $products = Product::whereIn('id', $data_id->keys())->get();

        return view('checkout', compact('regions', 'products'));
    }

    public function addToCart(Request $request, $lang, $id)
    {
        $product = Product::findOrFail($id);
        $product_lang = $product->products_lang->where('lang', $lang)->first();

        if ($request->session()->has('items')) {

            $items = $request->session()->get('items');
            $quantity = (isset($request->quantity)) ? $request->quantity : 1;

            $items['products_id'][$id] = [
                'id' => $id, 'quantity' => $quantity, 'slug' => $product->slug, 'title' => $product_lang['title'], 'img_path' => $product->path.'/'.$product->image, 'price' => $product_lang['price'],
            ];

            $request->session()->put('items', $items);
            $count = count($items['products_id']);
            $sum_price_items = 0;

            foreach ($items['products_id'] as $item) {
                $sum_price_item = $item['price'] * $item['quantity'];
                $sum_price_items += $sum_price_item;
            }

            return response()->json([
                'alert' => 'Товар обновлен', 'countItems' => $count, 'sumPriceItems' => $sum_price_items, 'quantity' => $request->quantity, 'slug' => $product->slug, 'title' => $product_lang['title'], 'img_path' => $product->path.'/'.$product->image, 'price' => $product_lang['price'],
            ]);
        }

        $items = [];
        $items['products_id'][$id] = [
            'id' => $id, 'quantity' => 1, 'slug' => $product->slug, 'title' => $product_lang['title'], 'img_path' => $product->path.'/'.$product->image, 'price' => $product_lang['price'],
        ];

        $request->session()->put('items', $items);

        return response()->json([
            'alert' => 'Товар обновлен', 'countItems' => 1, 'slug' => $product->slug, 'title' => $product_lang['title'], 'img_path' => $product->path.'/'.$product->image, 'price' => $product_lang['price'],
        ]);
    }

    // Ajax removing item from cart
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

    public function storeOrder(Request $request, $lang)
    {
        $this->validate($request, [
            'surname' => 'required|min:2|max:255',
            'name' => 'required|min:2|max:255',
            // 'email' => 'required|email|max:255',
            'phone' => 'required|min:5',
            'region_id' => 'numeric',
            'address' => 'required',
            'count' => 'required',
        ]);

        $items = $request->session()->get('items');
        $data_id = collect($items['products_id']);
        $products = Product::whereIn('id', $data_id->keys())->get();

        $sum_count_products = 0;
        $sum_price_products = 0;

        foreach ($products as $product) {
            // $product_lang = $product->products_lang->where('lang', $lang)->first();
            // $sum_count_products += $items['products_id'][$product->id]['quantity'];
            $sum_price_products += $items['products_id'][$product->id]['quantity'] * $items['products_id'][$product->id]['price'];
        }

        $order = new Order;
        $order->user_id = ((Auth::check())) ? Auth::id() : 0;
        $order->name = $request->surname.' '.$request->name;
        $order->phone = $request->phone;
        $order->email = $request->email;
        $order->company_name = $request->company_name;
        $order->data_1 = $request->notes;
        $order->data_2 = $request->postcode;
        $order->data_3 = '';
        $order->legal_address = '';
        $order->region_id = ($request->region_id) ? $request->region_id : 0;
        $order->address = $request->address;
        $order->count = serialize($request->count);
        $order->price = $products->sum('price');
        $order->amount = $sum_price_products;
        $order->delivery = 1;
        $order->payment_type = 1;
        $order->save();

        $order->products()->attach($data_id->keys());

        $name = $request->name;

        // Email subject
        $subject = "Baimarket - Новая заявка от $request->name";

        $headers = "From: info@baimarket.kz \r\n" .
                   "MIME-Version: 1.0" . "\r\n" . 
                   "Content-type: text/html; charset=UTF-8" . "\r\n";

        $content = view('partials.mail-new-order', ['order' => $order])->render();

        try {
            mail('issayev.adilet@gmail.com', $subject, $content, $headers);

            $status = 'alert-success';
            $message = 'Ваш заказ принят!';

            // Mail::send('vendor.mail.html.layout', ['order' => $order], function($message) use ($name) {
            //     $message->to(['abdulaziz.abishov@gmail.com', 'issayev.adilet@gmail.com'], 'Baimarket')->subject('Baimarket - Новый заказ от '.$name);
            //     $message->from('electron.servant@gmail.com', 'Electron Servant');
            // });

            $response = view('partials.mail-client-order', ['order' => $order])->render();

            mail($order->email, 'Baimarket - ваш заказ: '.$order->id, $response, $headers);

        } catch (Exception $e) {

            $status = 'alert-danger';
            $message = 'Произошла ошибка: '.$e->getMessage();
        }

        $request->session()->forget('items');

        return redirect($lang.'/cart')->with([
            'info' => $message
        ]);
    }

    // Get method removing item from cart
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

        return redirect($lang.'/cart');
    }
}