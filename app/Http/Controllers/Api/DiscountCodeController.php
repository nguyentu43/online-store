<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DiscountCode;
use App\ProductSku;

class DiscountCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->middleware('can:create,App\DiscountCode')->only('store');
        $this->middleware('can:update,discount_code')->only('update');
        $this->middleware('can:delete,discount_code')->only('destroy');
    }

    public function index(Request $request)
    {
        if($request->has('code'))
        {
            $discount_code = DiscountCode::find($request->get('code'));

            $id = $request->get('id');

            if($discount_code && (!empty($id) && $discount_code->id != $id))
            {
                return response()->json(['status' => 'ok', 'exists' => true, 'data' => $discount_code]);
            }
            else
            {
                return response()->json(['status' => 'ok', 'exists' => false, 'msg' => 'Không tồn tại mã giảm giá này']);
            }
        }

        return DiscountCode::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        if(DiscountCode::create($data))
        {
            return response()->json(['status' => 'ok']);
        }
    }

    public function check(Request $request)
    {
        if($request->has('code') && $request->has('items') && $request->has('user_id'))
        {
            $code = DiscountCode::where('code', $request->code)->first();

            if($code)
            {
                $items = [];

                foreach($request->items as $item)
                {
                    $items[] = $item['cart'];
                }

                $data = $code->check($request->user, $items);
                $data['status'] = 'ok';
                return response()->json($data);
            }
            else
            {
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Mã giảm giá không tồn tại'
                ]);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Lỗi tham số không hợp lệ'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DiscountCode $discount_code)
    {
        return $discount_code;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DiscountCode $discount_code)
    {
        if($discount_code->update($request->all()))
        {
            return response()->json(['status' => 'ok']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DiscountCode $discount_code)
    {
        if($discount_code->delete())
        {
            return response()->json(['status' => 'ok']);
        }
    }
}
