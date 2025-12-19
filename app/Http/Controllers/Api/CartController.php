<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
class CartController extends Controller
{
    public function index(Request $request){
        
        $cartItems=CartItem::with('product')->where('user_id',$request->user()->id)->get();
        return response()->json($cartItems);
    }
    public function store(Request $request){
        $request->validate([
            'product_id'=>"required|exists:products,id",
            "quantity"=>"required|integer|min:1"
        ]);
        $usersId=$request->user()->id;
        $productId=$request->product_id;
        $quantity=$request->quantity;

        $cartItems=CartItem::where('user_id',$usersId)->where('product_id',$productId)->first();
            if($cartItems){
                $cartItems->quantity+=$quantity;
                $cartItems->save();
            } else{
                $cartItems=CartItem::create([
                    "user_id"=>$usersId,
                    "product_id"=>$productId,
                    "quantity"=>$quantity
                ]);
            }
            return response()->json([
                "message"=>"them vao gio hang thanh cong",
                "data"=>$cartItems
            ]);
    }
    public function updateCart(Request $request,$id){
        $request->validate([
            "quantity"=>"required|integer|min:1"
        ]);
        $user_id = $request->user()->id;
        $cartItems=CartItem::where("user_id",$user_id )->where('id',$id)->first();
        if(!$cartItems){
            return response()->json(["message"=>"khong tim thay san pham"]); 
        }
        $cartItems->quantity=$request->quantity;
        $cartItems->save();
        return response()->json([
            "message"=>"cap nhat gio hang thanh cong",
            "data"=>$cartItems
        ]);
    }
    public function deleteCart($id,Request $request){
        $cartItems=CartItem::where('user_id',$request->user()->id)->where('id',$id)->first();
        if(!$cartItems){
            return response()->json(["message"=>"hang khong ton tai"]);
        }
        $cartItems->delete();
        return response()->json([
            "message"=>"xoa thanh cong",
            "date"=>$cartItems
        ]);
    }
    public function checkOut(Request $request){
        $request->validate([
            "shipping_address"=>"required|string",
            "phone"=>"required|string"
        ]);
        $user=$request->user();
        $cartItems=CartItem::with('product')->where('user_id',$user->id)->get();
        if($cartItems->isEmpty()){
            return response()->json(['message'=>"gio hang rong"],400);
        }
        $totalprice=0;
        foreach($cartItems as $item){
            $totalprice += $item->product->price * $item->quantity;
        }
        DB::beginTransaction();
        try{
            $order=Order::create([
                "user_id"=>$user->id,
                "shipping_address"=>$request->shipping_address,
                "phone"=>$request->phone,
                "total_price"=>$totalprice,
                "status"=>"pending"
            ]);
            foreach($cartItems as $item){
                OrderItem::create([
                    "order_id"=>$order->id,
                    "product_id"=>$item->product_id,
                    "quantity"=>$item->quantity,
                    "price"=>$item->product->price
                ]);
            }

            // Mail::to($user->email)->send(new OrderPlaced($order));
            CartItem::where('user_id',$user->id)->delete();
            DB::commit();

            return response()->json([
                "message"=>"dat hang thanh cong",
                "data"=>$order->id
            ]);
        } catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
