<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductSku;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductSku as ProductSkuResource;
use App\Http\Resources\ProductSkuForCart;

class ProductSkuController extends Controller
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
        if($request->has('list'))
        {
            $product_skus = ProductSku::whereRaw("FIND_IN_SET(id, ?)", [ $request->get('list') ])->get();
            $product_skus = $product_skus->filter(function($item){

                return $item->product->enable == 1;

            });

            return ProductSkuForCart::collection($product_skus);
        }

        if($request->user('api')->can('update', $product) || $product->enable == 1)
                return ProductSkuResource::collection($product->skus);
            else
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tồn tại sản phẩm này'
                ], 404);
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
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'name' => 'required'
        ])->validate();

        $sku = $request->only([ 'name', 'price', 'quantity' ]);
        $sku['sku'] = $this->createSku($product).'-'.$sku['name'];
        $sku = $product->skus()->create($sku);

        if($request->discount)
        {
            $sku->discount()->create($request->discount);
        }

        if($sku)
        {
            return response()->json([
                'status' => 'ok',
                'id' => $sku->id
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, $id)
    {
        //
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
            'quantity' => 'numeric|min:0',
            'price' => 'numeric|min:0'
        ])->validate();

        $sku = $product->skus->find($id);
        $sku->update($request->all());
        $sku->sku = $this->createSku($product).'-'.$sku->name;

        if($request->discount)
        {
            $discount = $sku->discount;

            if($discount)
                $discount->update($request->discount);
            else
                $sku->discount()->create($request->discount);
        }
        else
        {
            if($sku->discount)
                $sku->discount->delete();
        }

        if($sku->save())
        {
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
    public function destroy(Product $product, $id)
    {
        if($product->skus->count() > 1)
        {
            if($product->skus->find($id)->delete())
            {
                return response()->json([
                    'status' => 'ok'
                ]);
            }
        }
        else
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không thể xoá SKU mặc định'
            ], 404);
        }
    }

    private function createSku($product)
    {
        $brand_name = mb_substr($product->brand->name, 0, 3);
        $arr = explode(' ', $product->type->name);
        $product_type_name = array_reduce($arr, function ($carry, $value){ $carry .= mb_substr($value, 0, 1); return $carry; });
        $id = str_pad($product->id, 6, 0, STR_PAD_LEFT);

        return strtoupper(iconv('utf-8', 'ascii//translit', $brand_name.$product_type_name.$id));
    }
}
