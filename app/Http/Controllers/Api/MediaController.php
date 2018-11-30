<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductSku;
use App\Media;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:update,product');
    }

    public function store(Request $request, Product $product, ProductSku $sku)
    {
    	if($sku->media_list()->create($request->all()))
        	return response()->json([
                'status' => 'ok'
            ]);
    }

    public function destroy(Request $request, Product $product, ProductSku $sku, $id)
    {
        $media = $sku->media_list->find($id);
        Storage::disk('public')->delete($media->url);
    	if($media->delete())
    	{
    		return response()->json([
                'status' => 'ok'
            ]);
    	}
    }
}
