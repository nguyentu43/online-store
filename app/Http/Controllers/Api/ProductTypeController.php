<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductType;
use App\Http\Resources\ProductType as ProductTypeResource;
use Illuminate\Support\Facades\Validator;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:create,App\ProductType')->only('store');
        $this->middleware('can:update,producttype')->only('update', 'attach', 'detach');
        $this->middleware('can:delete,producttype')->only('destroy');
    }

    public function index()
    {
        return ProductTypeResource::collection(ProductType::all());
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

        if(ProductType::create($request->all()))
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
    public function show(Request $request, $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductType $producttype)
    {
        Validator::make($request->all(), [
            'name' => 'required'
        ])->validate();

        if($producttype->update($request->all()))
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
    public function destroy(ProductType $producttype)
    {
        if($producttype->delete())
            return response()->json([
                'status' => 'ok'
            ]);
    }

    public function detach(ProductType $producttype, $attr_id)
    {
        if($producttype->attributes()->detach($attr_id))
            return response()->json([
                'status' => 'ok'
            ]);
    }

    public function attach(ProductType $producttype, $attr_id)
    {
        if($producttype->attributes()->attach($attr_id))
            return response()->json([
                'status' => 'ok'
            ]);
    }
}
