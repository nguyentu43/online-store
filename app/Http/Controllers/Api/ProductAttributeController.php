<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductAttribute;
use App\ProductType;
use Illuminate\Support\Facades\Validator;

class ProductAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:create,App\ProductType')->except('index', 'show');
    }

    public function index(Request $request)
    {
        if($request->get('product_type_id'))
        {
            return ProductType::findOrFail($request->get('product_type_id'))->attributes;
        }

        return ProductAttribute::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Validator::make($request->all(), [
            'name' => 'required'
        ])->validate();

        $attributes = ProductAttribute::create($request->all());

        if($request->input('product_type_id'))
            if(ProductType::findOrFail($request->input('product_type_id'))->attributes()->attach($attributes->id))
                return response()->json([
                    'status' => 'ok'
                ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductAttribute $attribute)
    {
        if($attribute->update($request->all()))
            return response()->json([
                'status' => 'ok'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductAttribute $attribute)
    {
        if($attribute->delete())
            return response()->json([
                'status' => 'ok'
            ]);
    }
}
