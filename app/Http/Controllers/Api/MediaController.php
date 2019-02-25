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
        if(Gate::allows('storage.store'))
        {
            if($request->hasFile('file') && $request->file->isValid())
            {
                $path = Storage::disk('public')->putFile('images', $request->file);

                if($sku->media_list()->create([
                    'url' => $path
                ]))
                    return response()->json([
                        'status' => 'ok',
                        'path' => $path
                    ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'File tải lên không hợp lệ'
            ], 404);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Tài khoản chưa được cấp quyền'
        ], 405);
    }

    public function destroy(Request $request, Product $product, ProductSku $sku, $id)
    {
        if(Gate::allows('storage.destroy'))
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

        return response()->json([
            'status' => 'error',
            'message' => 'Tài khoản chưa được cấp quyền'
        ], 405);    
    }
}
