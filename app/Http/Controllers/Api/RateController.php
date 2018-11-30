<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Rate as RateResource;
use App\Product;
use App\Rate;
use Illuminate\Support\Facades\DB;
use App\Comment;
use Illuminate\Support\Facades\Storage;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:create,App\Rate')->only('store');
        $this->middleware('can:update,rate')->only('update');
        $this->middleware('can:delete,rate')->only('destroy');
    }

    public function index(Request $request)
    {
        $product = Product::findBySlug($request->product_slug);
        $product_skus = $product->skus()->pluck('id');
        $group = Rate::query()->whereIn('product_sku_id', $product_skus )->groupBy('rate.rate')->select(DB::raw("rate, count(id) as count"))->get();

        $rate_products = Rate::query();

        if($request->has('order'))
        {
            $order = $request->order;
            $type = $order[0] == '-' ? 'desc' : 'asc';
            $order = str_replace('-', '', $order);
            switch ($order) {
                case 'date':
                    $rate_products = $rate_products->orderBy('updated_at', $type);
                    break;
                default:
                    break;
            }
        }

        $list = RateResource::collection($rate_products->whereIn('product_sku_id', $product_skus)->paginate($request->get('per_page', 10)));

        return [ 
            'list' => json_decode($list->toJSON()),
            'group' => $group,
            'rate_product' => $product->rate
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

        $rate = Rate::create($request->all());

        if(!empty($request->comment))
        {
            $comment = [
                'content' => $request->comment,
                'user_id' => $request->user_id
            ];

            $rate->comments()->create($comment);
        }

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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rate $rate)
    {
        $rate->update($request->all());
        if($request->has('comment') && $request->comment)
        {
            if($rate->comment)
            {
                $rate->comment->update(['content' => $request->comment]);
            }
            else
            {
                $comment = [
                    'content' => $request->comment,
                    'user_id' => $request->user_id
                ];

                $rate->comments()->create($comment);
            }
        }
        else
        {
            if($rate->comment)
                $rate->comment->delete();
        }

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
    public function destroy(Request $request, Rate $rate)
    {
        if(!empty($rate->images) && count($rate->image) > 0)
        {
            foreach($rate->images as $image)
                Storage::disk('public')->delete($image);
        }

        if($rate->delete())
        {
            return response()->json([
                'status' => 'ok'
            ]);
        }
    }
}
