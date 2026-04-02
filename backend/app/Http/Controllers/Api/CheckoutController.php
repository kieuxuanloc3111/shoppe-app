<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\History;
use App\Mail\MailNotify;

class CheckoutController extends Controller{
    public function checkout(Request $request)
    {
        
        $user = Auth::user();

        if(!$user){
            return response()->json([
                'response'=>'error',
                'message'=>'Unauthenticated'
            ],401);
        }

        $cart = $request->cart;

        if(empty($cart)){
            return response()->json([
                'response'=>'error',
                'message'=>'Cart empty'
            ],400);
        }

        // tính total
        $total = 0;
        foreach($cart as $item){
            $total += $item['price'] * $item['qty'];
        }

        // lưu history
        History::create([
            'id_user'=>$user->id,
            'name'=>$request->name,
            'email'=>$user->email,
            'phone'=>$request->phone,
            'price'=>$total
        ]);

        // ✉️ gửi mail
        try {

            Mail::to($user->email)->send(
                new MailNotify($cart, $total, $user)
            );

        } catch (\Exception $e) {

            return response()->json([
                'response'=>'error',
                'message'=>'Send mail failed',
                'error'=>$e->getMessage()
            ],500);

        }

        return response()->json([
            'response'=>'success',
            'message'=>'Checkout success & mail sent',
            'total'=>$total
        ],200);
    }
}
