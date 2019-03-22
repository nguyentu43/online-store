<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\ProductSku;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        //$this->middleware('can:create,App\Order');
    }

    public function index(Request $request)
    {
        $cart = request()->user()->cart->load('products');

        foreach($cart->products as $sku)
        {
            if(!$sku->product->enable)
                $cart->products()->detach($sku->id);
        }

        return request()->user()->cart->load('products');
    }

    public function destroy()
    {
        request()->user()->cart->products()->detach();

        return response()->json([
            'status' => 'ok'
        ]);
    }

    public function addItem(Request $request)
    {
        function add($id, $quantity){

            if(ProductSku::find($id))
            {
                $item = request()->user()->cart->products()->where('product_sku_id', $id)->get()->first();

                if($item)
                {
                    request()->user()->cart->products()->updateExistingPivot($id, [ 'quantity' => $item->pivot->quantity + $quantity ]);
                }
                else
                    request()->user()->cart->products()->attach($id, [ 'quantity' => $quantity ]);
            }
        }

        if($request->input('quantity') > 0)
        {
            add($request->input('product_sku_id'), $request->input('quantity'));
        }

        if($request->input('data'))
        {
            $data = json_decode($request->input('data'));

            foreach($data as $item)
            {
                add($item->product_sku_id, $item->quantity);
            }
        }

        return response()->json([
            'status' => 'ok'
        ]);
        
    }

    public function changeItem(Request $request, $id)
    {
        if($request->input('quantity') > 0)
        {
            ProductSku::findOrFail($id);
            request()->user()->cart->products()->updateExistingPivot($id, [ 'quantity' => $request->input('quantity') ]);
        }

        return response()->json([
            'status' => 'ok'
        ]);
    }

    public function removeItem(Request $request, $id)
    {
        request()->user()->cart->products()->detach($id);

        return response()->json([
            'status' => 'ok'
        ]);
    }
}
