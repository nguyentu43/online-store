<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class StorageController extends Controller
{

    public function store(Request $request)
    {
        if(Gate::allows('storage.store'))
        {
            if($request->hasFile('file') && $request->file->isValid())
            {
                $path = Storage::disk('public')->putFile('images', $request->file);

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


    public function destroy(Request $request)
    {
        if(Gate::allows('storage.destroy'))
        {
            if(Storage::disk('public')->delete($request->name))
            {
                return response()->json([
                    'status' => 'ok'
                ]);
            }
            else
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lỗi xoá file'
                ], 404);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Tài khoản chưa được cấp quyền'
        ], 405);	
    }
}
