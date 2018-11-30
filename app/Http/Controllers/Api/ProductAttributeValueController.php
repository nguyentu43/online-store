<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductAttribute;
use App\ProductType;
use Illuminate\Support\Facades\Validator;

class ProductAttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:update,product')->except('index', 'show');
    }


    public function index(Request $request, Product $product)
    {
        if($request->get('filter') == 'not_in')
        {
            return ProductAttribute::whereIn('id', array_diff($product->type->attributes->pluck('id')->all(), $product->attributes->pluck('id')->all()))->get();

        }

        return $product->attributes;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product)
    {
        Validator::make($request->all(), [
            'product_attribute_id' => 'required',
            'value' => 'required'
        ])->validate();

        if($product->attributes()->attach($request->product_attribute_id, ['value' => $request->value]))
        {
            $product->searchable();
            return response()->json([
                'status' => 'ok'
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Product $product, $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, $id)
    {
        Validator::make($request->all(), [
            'product_attribute_id' => 'required',
            'value' => 'required'
        ])->validate();

        if($product->attributes()->updateExistingPivot($id, ['value' => $request->value]))
        {
            $product->save();
            return response()->json([
                'status' => 'ok'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Product $product, $id)
    {
        if($product->attributes()->detach($id))
        {
            $product->searchable();
            return response()->json([
                'status' => 'ok'
            ]);
        }
    }
}
