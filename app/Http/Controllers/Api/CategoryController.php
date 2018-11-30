<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Product as ProductResource;
use App\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:create,App\Category')->only('store');
        $this->middleware('can:update,category')->only('update');
        $this->middleware('can:delete,category')->only('destroy');
    }

    public function index(Request $request)
    {
        if($request->skip_prop == 'enable' && $request->user('api')->can('create', Category::class))
            return CategoryResource::collection(Category::whereNull('parent_id')->get());
        return CategoryResource::collection(Category::whereNull('parent_id')->where('enable', 1)->get());
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

        $category = Category::create($request->all());
        if($category)
            return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $category->slug = null;
        if($category->update($request->except('slug')))
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
    public function destroy(Category $category)
    {
        if(!empty($category->image))
            Storage::disk('public')->delete($category->image);
        if($category->delete())
            return response()->json([
                'status' => 'ok'
            ]);
    }
}
