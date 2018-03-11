<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cart;
use App\CartItems;
use App\Product;
use Auth;

class CartController extends Controller
{
    public function getCartByUser() {
      $cart = Cart::where('userId', Auth::user()->id)->where('status', 'ACTIVE', 'AND')->first();
      if ($cart) {
        $json['items'] = $cart->items;
      } else {
        $cart = new Cart;
        $cart->userId = Auth::user()->id;
        $cart->save();
      }

      $json['cart'] = $cart;
      return response()->json($json, 200);
    }

    public function getCartByGuestId($guestId) {
      $cart = Cart::where('guestId', $guestId)->where('status', 'ACTIVE', 'AND')->first();
      if ($cart) {
        $json['items'] = $cart->items;
      } else {
        $cart = new Cart;
        $cart->guestId = $guestId;
        $cart->save();
      }

      $json['cart'] = $cart;
      return response()->json($json, 200);
    }

    public function addToCart(Request $request) {
      $authId = $request->has('userId') ? $request->input('userId') : $request->input('guestId');
      $cartId = $request->input('cartId');
      $cart = Cart::find($cartId);
      if ($authId && $cart && $cart->status == 'ACTIVE') {
        $product = Product::find($request->input('productId'));
        if (!$product) return response()->json(['error' => 'Product not found!', 400]);
        $cart->items()->save([
          'productId' => $request->input('productId'),
          'quantity' => $request->input('quantity')
          'reservePrice' => $product->price,
          'finalPrice' => 0.00
        ]);
        $json['cart'] = $cart;
        $json['items'] = $cart->items;
        return response()->json($json, 200);
      }

      return response()->json(['error' => 'Cart not found'], 400);
    }

    public function removeToCart ($itemId) {
      $item = CartItems::find($itemId);
      if (!$item) return response()->json(['error' => 'Item not found'], 400);
      $item->delete();
      return response()->json(['message' => 'Success'], 200);
    }
}
