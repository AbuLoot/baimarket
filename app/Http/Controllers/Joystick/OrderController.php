<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use App\Http\Controllers\Joystick\Controller;
use App\Order;
use App\Region;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->paginate(50);

        return view('joystick-admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        return view('joystick-admin.orders.show', compact('order'));
    }

    public function edit($lang, $id)
    {
        $order = Order::findOrFail($id);
        $regions = Region::all();

        return view('joystick-admin.orders.edit', compact('order', 'regions'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|min:5',
            'city_id' => 'numeric',
            'address' => 'required',
        ]);

        $order = Order::findOrFail($id);
        // $order->name = $request->name;
        // $order->email = $request->email;
        // $order->phone = $request->phone;
        $order->company_name = $request->company_name;
        $order->data_1 = $request->data_1;
        $order->data_2 = $request->data_2;
        $order->data_3 = $request->data_3;
        $order->legal_address = $request->legal_address;
        $order->city_id = ($request->city_id) ? $request->city_id : 0;
        $order->address = $request->address;
        $order->delivery = $request->delivery;
        $order->payment_type = $request->payment_type;
        // $order->count = serialize($request->count);
        // $order->price = $products->sum('price');
        // $order->amount = $sumPriceProducts;
        $order->status = $request->status;
        $order->save();

        return redirect($lang.'/admin/orders')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $order = Order::find($id);
        $order->delete();

        return redirect($lang.'/admin/orders')->with('status', 'Запись удалена!');
    }
}