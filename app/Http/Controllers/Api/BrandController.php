<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Brand as BrandResource;
use App\Brand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:create,App\Brand')->only('store');
        $this->middleware('can:update,brand')->only('update');
        $this->middleware('can:delete,brand')->only('destroy');
    }

    public function index(Request $request)
    {
        if($request->get('list'))
        {
            return BrandResource::collection(Brand::whereIn('id', explode(',', $request->get('list')))->get());
        }
        return BrandResource::collection(Brand::all());
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

        if(Brand::create($request->all()))
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
    public function show(Brand $brand)
    {
        return new BrandResource($brand);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {

        if($brand->update($request->all()))
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
    public function destroy(Brand $brand)
    {
        Storage::disk('public')->delete($brand->image);
        if($brand->delete())
            return response()->json([
                'status' => 'ok'
            ]);
    }
}
