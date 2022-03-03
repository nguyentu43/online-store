<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\Category as CategoryResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProductSkuOrder as ProductSkuResource;
use App\ProductSku;
use App\ProductAttribute;
use App\ProductType;
use App\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:create,App\Product')->only('store');
        $this->middleware('can:update,product')->only('update');
        $this->middleware('can:delete,product')->only('destroy');
    }

    public function index(Request $request)
    {

        if(trim($request->keyword) == '')
        {
            $products = DB::table('products');
        }
        else
        {
            $array_id = array_map(function($item){
                return $item->id;
            }, Product::search($request->get('keyword'))->get()->all());
            $products = DB::table('products')->whereIn('products.id', $array_id);
        }

        if($request->has('list'))
        {
            $products = $products->whereIn('products.id', explode(',', $request->get('list')));
        }

        if($request->has('category'))
        {
            $products = $products->where('category_id', $request->get('category'));
            $products = $products
                        ->join('categories', 'products.category_id', '=', 'categories.id')
                        ->orWhereIn("categories.order", explode(",",$request->get('category')));
            $products = $products->distinct()->select(DB::raw('products.*'))->pluck('id');
            $products = DB::table('products')->whereIn('products.id', $products);
        }

        if(!empty($request->get('brand')))
        {
            $products = $products->join('brands', 'brand_id', '=', 'brands.id')
                        ->where('brands.name', $request->get('brand'));
        }

        if($request->has('type'))
        {
            $products = $products->where('product_type_id', $request->get('type'));
        }

        if($request->has('random'))
        {
            $products = $products->inRandomOrder();
        }

        $products = $products->join('product_skus', 'products.id', '=', 'product_id');
        $max_price = $products->max('product_skus.price');

        if($request->has('sort'))
        {
            $sort = $request->get('sort');
            $mode = $sort[0] == '-' ? 'desc' : 'asc';

            if(strstr($sort, 'price'))
                $products = $products->orderBy('product_skus.price', $mode);
            else if(strstr($sort, 'discount'))
            {
                
                $products = $products->leftJoin('discounts', 'product_skus.id', '=', 'product_sku_id')->orderBy('discounts.value', $mode);
            }
            else if(strstr($sort, 'newest'))
            {
                $products = $products->orderBy('products.updated_at', 'desc');
            }
            else if(strstr($sort, 'bestsell'))
            {
                $products = $products->leftJoin('order_detail', 'product_skus.id', '=', 'order_detail.sku_id');
            }
        }

        if(!empty($request->get('range'))){

            $range = explode(' - ', $request->get('range'));

            $products = $products->whereBetween('product_skus.price', $range);
        }

        if(!empty($request->get('attrs')))
        {
            global $attrs;
            $attrs = get_object_vars(json_decode($request->attrs));

            if(count($attrs) > 0)
            {
                $products = $products
                        ->join('product_product_attributes', function($join){
                            global $attrs;
                            $join->on('products.id', '=', 'product_product_attributes.product_id')
                                 ->whereIn('product_attribute_id', array_keys($attrs))
                                 ->whereIn('value', array_values($attrs));
                        });
            }
        }

        if(!($request->skip_prop == 'enable' && $request->user('api')->can('create', 'App\Product')))
        {
            $products = $products->where('enable', 1);
        }

        $products = $products->distinct()->select(DB::raw('products.*'));

        $all = $products->get()->count();
        $brands = $products->pluck('brand_id')->toArray();
        $brands = array_values(array_unique($brands));

        $per_page = $request->get('per_page', 16);
        $products = $products->skip(($request->get('page', 1) - 1) * $per_page)->take($per_page);

        $product_types = array_unique($products->pluck('product_type_id')->all());

        $products = Product::hydrate($products->get()->all());

        $attr_filter = [];

        if($products->count() > 0)
            $category = $products->first()->category->orderPath;
        else
            $category = [];

        foreach($products as $product)
        {
            $category = array_intersect($category, $product->category->orderPath);

            if(in_array($product->product_type_id, $product_types))
            {
                foreach($product->attributes as $attr)
                {
                    if($attr->filterable == 1)
                    {
                        if(!array_key_exists(strval($attr->id), $attr_filter))
                        {
                            $attr_filter[strval($attr->id)] = [
                                'name' => $attr->name,
                                'values' => []
                            ];
                        }

                        if(!in_array($attr->pivot->value, $attr_filter[strval($attr->id)]['values']))
                        {
                            $attr_filter[strval($attr->id)]['values'][] = $attr->pivot->value;
                        }
                    }
                }
            }
        }

        if(count($category))
        {
            $category = Category::find($category[count($category) - 1]);
        }
        else
        {
            $category = Category::find($request->category);
        }

        if($category)
        {
            if(empty($category->children))
                $category = array_merge([-2], $category->orderPath, [$category->id]);
            else
                $category = array_merge([-2], $category->orderPath);
        }
        else
            $category = [-2, -1];
        
        return [
            'products' => ProductResource::collection($products),
            'meta' => [
                'total' => $all,
                'brands' => $brands,
                'attr_filter' => $attr_filter,
                'max_price' => $max_price,
                'category' => $category
            ]
        ];
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
            'name' => 'required',
            'brand_id' => 'required',
            'category_id' => 'required',
            'product_type_id' => 'required'
        ])->validate();

        $product = Product::create($request->all());

        if($product)
        {
            event(new \App\Events\ProductEvent($product));
            return response()->json([
                'status' => 'ok',
                'slug' => $product->slug
            ]);
        }

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Product $product)
    {

        if(!$product->enable && !$request->user('api')->can('update', $product)) 
        {
            return response()->json( [ 'status' => 'error', 'message' => 'Product not found' ], 404);
        }

        $categories[] = $product->category->load('children');

        if($product->category->order)
        {

            $categories = array_merge(Category::whereIn("id", explode(',', $product->category->order))->get()->load('children')->all(), $categories);
        }

        return ['categories' => $categories, 'product' => new ProductResource($product)];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {

        $product->slug = null;
        if($product->update($request->all()))
        {
            return response()->json([
                'status' => 'ok',
                'slug' => $product->slug
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if($product->delete())
            return response()->json([
                'status' => 'ok'
            ]);
    }

    public function getProductSoldOut(Request $request)
    {
        $skus = ProductSku::with('product')->where('quantity', '<' , 10)->get();
        return $skus;
    }
}
