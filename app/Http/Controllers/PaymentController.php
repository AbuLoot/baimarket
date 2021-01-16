<?php

namespace App\Http\Controllers;

use App\PaymentLog;
use App\PG_Signature;
use App\User;
use App\Card;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function PayBox($amount, $user_id)
    {
        $user = User::find($user_id);
        $card = Card::where('slug', $user->profile->card_type)->first();
        $amount = $card->price;

        $arrReq = [
            'pg_merchant_id' => 532282,
            'pg_amount' => $amount,
            'pg_description' => '5 Second payment',
            'pg_order_id' => $user_id,
            'pg_salt' => mt_rand(21, 43433),
            'pg_result_url' => url('PayBoxResult')
        ];
        $arrReq['pg_sig'] = PG_Signature::make('payment.php', $arrReq, 'oXVtF2dnuechFVeW');

        $query = http_build_query($arrReq);

        $url = 'https://www.paybox.kz/payment.php?'.$query;

        PaymentLog::create([
            'user_id' => $user_id,
            'amount' => $amount,
            'status' => 1,
            'description' => ''
        ]);

        return redirect()->to($url);
    }

    public function PayBoxResult(Request $request)
    {
        if ($request['pg_result']) {

            $user = User::find($request['pg_order_id']);
            $user->balance += $request['pg_amount'];
            $user->save();

            $date = date('Y-m-d');
            $term = date('Y-m-d', strtotime($date. ' + 30 days'));

            $user->privilege->term = $term;
            $user->privilege->status = 1;
            $user->privilege->save();

            $payment = PaymentLog::where('user_id', $request['pg_order_id'])->orderBy('id', 'DESC')->first();
            $payment->status = PaymentLog::TYPE_SUCCESS;
            $payment->save();

            $arrReq = [
                'pg_merchant_id' => 532282,
                'pg_salt' => mt_rand(21, 43433)
            ];
            $pg_sig = PG_Signature::make('payment.php', $arrReq, 'oXVtF2dnuechFVeW');
            $pg_salt = str_random(10);

            $xmlResponce = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<response>
<pg_salt>$pg_salt</pg_salt>
<pg_status>ok</pg_status>
<pg_description>Клиент оплатил абонемент</pg_description>
<pg_sig>$pg_sig</pg_sig>
</response>
XML;

            return $xmlResponce;
        }

        return null;
    }
}
