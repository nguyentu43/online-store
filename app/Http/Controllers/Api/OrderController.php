<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Order as OrderResource;
use App\User;
use App\ProductSku;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\OrderStatus;
use App\OrderStatusCode;
use Carbon\Carbon;
use App\Mail\Order as OrderMail;
use Illuminate\Support\Facades\Mail;
use App\DiscountCode;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:create,App\Order')->only('create', 'createOrderFromCart', 'index');
        $this->middleware('can:update,order')->only('update', 'updateOrderStatus', 'payment');
        $this->middleware('can:delete,order')->only('delete');
    }

    public function index(Request $request)
    {
        $orders = Order::orderBy('orders.created_at', 'desc');

        if($request->has('order_code') && !empty($request->order_code))
        {
            $orders = $orders->where('order_code', 'like', '%'.$request->order_code.'%');
        }

        if(!empty($request->get('status_code')))
        {
            $orders = $orders->join('orders_order_status', 'orders.id', '=', 'orders_order_status.order_id')->groupBy('orders.id')->havingRaw('max(orders_order_status.status_id) = ?', [ $request->get('status_code', 4) ])->selectRaw('orders.*');
        }

        if(!empty($request->get('date_range')))
        {
            $orders = $orders->whereBetween('created_at', $request->get('date_range'));
        }

        if(!empty($request->get('user_id')))
        {
            $orders = $orders->where('user_id', $request->get('user_id'));
        }

        $income = 0;
        $data = $orders->with('status')->get();

        foreach($data as $item)
        {
            if($item->status->last()->id == 4)
                $income += $item->amount;
        }

        $total = $orders->count();

        $orders = $orders->paginate($request->get('per_page', 16));

        return [
            'orders' => OrderResource::collection($orders),
            'meta' => [
                'order_status' => OrderStatus::all(),
                'income' => $income,
                'total' => $total
            ]
        ];
    }

    public function getListUserOrders(Request $request)
    {
        $orders = Order::where('user_id', $request->user('api')->id)->orderBy('created_at', 'desc')->get();

        return [
            'orders' => OrderResource::collection($orders),
            'meta' => [
                'order_status' => OrderStatus::all()
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
        $user = User::find($request->user_id);
        $items = $request->items;

        $items = array_map(function($item){

            $sku = new \stdClass();
            $sku->id = $item['id'];
            $sku->pivot = (object) $item['pivot'];

            return $sku;

        }, $items);

        return $this->createOrder($request->all(), $user, $items);
    }

    private function paymentStripe($card, $amount)
    {

        // 'number' => '4242424242424242',
        // 'exp_month' => 10,
        // 'cvc' => 314,
        // 'exp_year' => 2020

        
        $stripe = Stripe::make('sk_test_BIoyl7l92wVmRZREi1GycsAH');
        //$stripe = Stripe::make(env('STRIPE_SECRET'));

        $exp = date_create($card['exp_date']);

        $token = $stripe->tokens()->create([
            'card' => [
                'number' => $card['card'],
                'exp_month' => date_format($exp, 'm'),
                'exp_year' => date_format($exp, 'Y'),
                'cvc' => $card['cvc']
            ]
        ]);

        $charge = $stripe->charges()->create([
            'card' => $token['id'],
            'currency' => 'VND',
            'amount' => $amount
        ]);

        return $charge;
    }

    public function payment(Request $request, Order $order)
    {
        if($order->payment->name == 'VISA')
        {
            $stripe = Stripe::make('sk_test_BIoyl7l92wVmRZREi1GycsAH');
            //$stripe = Stripe::make(env('STRIPE_SECRET'));

            if($order->charge)
            {
                $charge = $stripe->charges->find($order->charge);
                if($charge['status'] == 'succeeded')
                {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Lỗi thanh toán'
                    ], 403);
                }
            }
            
            $charge = $this->paymentStripe($request->all(), $order->amount);
            $order->charge_id = $charge['id'];
            $order->save();

            return $charge;
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Lỗi thanh toán'
        ], 403);
    }

    private function createOrder($data, $user, $items)
    {
        if(count($items) == 0)
            return response()->json([
                'status' => 'error',
                'message' => 'Giỏ hàng của bạn không có sản phẩm'
            ], 403);

        DB::beginTransaction();

        $amount_discount = 0;

        if(!empty($data['discount']) && !empty($data['discount']['code']))
        {
            $code = DiscountCode::where('code', $data['discount']['code'])->first();

            if(empty($code))
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mã giảm giá không tồn tại'
                ], 403);


            $items_check = [];

            foreach($items as $item)
            {
                $items_check[] = [
                    'sku_id' => $item->id,
                    'quantity' => $item->pivot->quantity
                ];
            }

            $response = $code->check($user->id, $items_check, true);

            if($response['status'] == 'error')
                return response()->json($response, 403);
            else
            {
                $data['discount']['value'] = $response['discount'];
                $amount_discount = $response['discount'];
            }
        }
        else
        {
            $data['discount'] = null;
        }

        $data['order_code'] = strtoupper(uniqid());
        $order = $user->orders()->create($data);
        $order->status()->attach(OrderStatusCode::DA_TIEP_NHAN);

        $amount = 0;

        foreach($items as $item)
        {
            $sku = ProductSku::find($item->id);

            if($sku->quantity < $item->pivot->quantity)
            {
                DB::rollback();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vượt quá số lượng đặt hàng'
                ], 403);
            }

            $sku->quantity -= $item->pivot->quantity;
            $sku->save();

            $discount = $sku->discount && $sku->discount->is_expire ? $sku->discount->value : 0;
            $order->items()->attach($sku->id, 
            [ 
                'discount' => $discount,
                'quantity' => $item->pivot->quantity,
                'price' => $sku->price
            ]);

            $amount += $sku->price * (1 - $discount) * $item->pivot->quantity;
        }

        $order->amount = $amount - $amount_discount;
        $order->payment()->associate($data['payment_method_id']);

        if($order->payment->name == 'VISA')
        {
            $data = $this->paymentStripe($data['card'], $amount);
            $order->charge_id = $data['id'];

            if($data['status'] == 'succeeded')
            {
                //
            }
        }

        $order->save();
        $user->cart->products()->detach();
        DB::commit();

        Mail::to($user)->queue(new OrderMail($order));
        return new OrderResource($order);
    }

    public function createOrderFromCart(Request $request)
    {
        $user = request()->user();
        $product_skus = $user->cart->products;
        return $this->createOrder($request->all(), $user, $product_skus);
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
    public function update(Request $request, Order $order)
    {
        DB::beginTransaction();

        $amount_discount = 0;

        if(!empty($order->discount))
        {
            $discount_code = DiscountCode::where('code', $order->discount['code'])->first();
            $discount_code->count++;
            $order->amount += $order->discount['value'];
            $discount_code->save();
        }

        $order->discount = null;
        $order->update($request->except('discount', 'items'));
        $items = $request->items;
        $amount = 0;

        foreach ($items as $item) {

            $sku = ProductSku::find($item['id']);

            $old_item = $order->items()->find($item['id']);

            if($old_item)
            {
                $sku->quantity += $old_item->pivot->quantity;
                $sku->save();
            }

            if($sku->quantity < $item['pivot']['quantity'])
            {
                DB::rollback();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vượt quá số lượng đặt hàng'
                ], 403);
            }

            $sku->quantity -= $item['pivot']['quantity'];
            $sku->save();

            if($old_item)
            {
                $discount = $old_item->pivot->discount;
                $order->items()->updateExistingPivot($sku->id, 
                [
                    'quantity' => $item['pivot']['quantity'],
                ]);
                $amount += $old_item->pivot->price * (1 - $discount) * $item['pivot']['quantity'];
            }
            else
            {
                $discount = $sku->discount && $sku->discount->is_expire ? $sku->discount->value : 0;
                $order->items()->attach($sku->id, 
                [ 
                    'discount' => $discount,
                    'quantity' => $item['pivot']['quantity'],
                    'price' => $sku->price
                ]);
                $amount += $sku->price * (1 - $discount) * $item['pivot']['quantity'];
            }
        }

        $remove_items = array_diff($order->items->pluck('id')->all(), array_map(function($item){ return $item['id']; }, $items));

        foreach ($remove_items as $id) {
            $sku = $order->items->find($id);
            $sku->quantity += $sku->pivot->quantity;
            $sku->save();
            $order->items()->detach($id);
        }
        
        $order->payment()->dissociate();
        $order->payment()->associate($request->payment_method_id);
        $order->save();

        $amount_discount = 0;

        if(!empty($request->discount) && !empty($request->discount['code']))
        {
            $code = DiscountCode::where('code', $request->discount['code'])->first();

            if(empty($code))
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mã giảm giá không tồn tại'
                ], 403);


            $items_check = [];

            foreach($order->items as $item)
            {
                $items_check[] = [
                    'sku_id' => $item->id,
                    'quantity' => $item->pivot->quantity
                ];
            }

            $response = $code->check($order->user_id, $items_check, true);

            if($response['status'] == 'error')
                return response()->json($response, 403);
            else
            {
                $data['discount']['value'] = $response['discount'];
                $amount_discount = $response['discount'];
            }
        }
        else
        {
            $order->discount = null;
        }

        $order->amount = $amount - $amount_discount;
        $order->save();
        DB::commit();

        return new OrderResource($order);        
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        if($request->has('status'))
        {
            $status = $request->status;

            if($status >= OrderStatusCode::DA_TIEP_NHAN && $status <= OrderStatusCode::HOAN_TRA)
            {
                $order->status()->sync(range(1, $status));
                Mail::to($request->user('api'))->queue(new OrderMail($order));
            }
            else if($status == OrderStatusCode::HUY)
            {
                if($order->status->last()->id == OrderStatusCode::DA_GIAO)
                {
                    return response()->json([
                        'status' => 'Lỗi giá trị không hợp lệ'
                    ], 405);
                }

                $order->status()->sync([OrderStatusCode::HUY], false);

                if($request->has('feedback'))
                {
                    $order->description .= " - [Lí do huỷ đơn hàng: ".$request->feedback."]";

                    if(!empty($order->discount))
                    {
                        $discount_code = DiscountCode::where('code', $order->discount['code'])->first();
                        $discount_code->count++;
                        $discount_code->save();
                        $order->discount = null;
                    }

                    $order->save();
                }
            }
            else
                return response()->json([
                    'status' => 'Lỗi giá trị không hợp lệ'
                ], 405);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        if($order->delete())
            return response()->json([
                'status' => 'ok'
            ]);
    }

    public function statistical(Request $request)
    {
        $now = Carbon::now();
        $year = $request->get('year', $now->year);
        $month = $request->get('month', $now->month);
        $by = $request->get('by', 'year');

        if($by == 'year')
        {
            $sql = <<<SQL
            select group_table.month, sum(group_table.amount) as 'sum', count(group_table.id) as 'count'
            from (SELECT orders.* ,month(orders.created_at) as 'month', year(orders.created_at) as 'year'
                  FROM orders join orders_order_status on orders.id = orders_order_status.order_id
                  group BY orders.id
                  having max(orders_order_status.status_id) = 4) as group_table
            where group_table.year = ?
            group by group_table.month

SQL;
            $orders = DB::select($sql, [ $year ]);
            $orders = collect($orders);
            $orders = $orders->keyBy('month');
            $data = $orders;
        }
        else if($by == 'month')
        {
            $sql = <<<SQL
            select group_table.day, sum(group_table.amount) as 'sum', count(group_table.id) as 'count'
            from (SELECT orders.*, month(orders.created_at) as 'month', year(orders.created_at) as 'year', day(orders.created_at) as 'day'
                  FROM orders join orders_order_status on orders.id = orders_order_status.order_id
                  group BY orders.id
                  having max(orders_order_status.status_id) = 4) as group_table
            where group_table.month = ? and group_table.year = ?
            group by group_table.day

SQL;
            $orders = DB::select($sql, [ $month, $year ]);
            $orders = collect($orders);
            $orders = $orders->keyBy('day');
            $data = $orders;
        }
        else if($by == 'order_status')
        {
            $sql = <<<SQL
            select table2.month, table2.status_id, sum(table2.count) as sum, name
            from
                (select table1.status_id, count(table1.id) as count, month(table1.created_at) as 'month'
                from (
                    SELECT orders.id, orders.created_at , max(orders_order_status.status_id) as status_id
                    FROM orders join orders_order_status on orders.id = orders_order_status.order_id
                    where year(orders.created_at) = ?
                    group BY orders.id
                    ) as table1
                group by table1.status_id
                ) as table2 join order_status on table2.status_id = order_status.id
            group by table2.month, table2.status_id
            order by table2.status_id
SQL;
            $data = DB::select($sql, [ $year ]);
            $data = collect($data);
            $data = $data->filter(function($item){
                return $item->status_id >= OrderStatusCode::DA_GIAO;
            });
            $data = $data->groupBy('month');
        }
        else if($by == 'bestsell')
        {
            $where = $request->has('month') ? 'month(orders.created_at) = ? and year(orders.created_at) = ? ' : 'year(orders.created_at) = ?';
            $params = $request->has('month') ? [ $month, $year ] : [ $year ];
            $sql = <<<SQL
            select product_skus.id as 'sku_id', product_skus.name as 'sku_name', products.id as 'product_id', products.name as 'product_name', count(products.id) as count
            from
                (select table1.id
                from
                    (select orders.id, max(order_status.id) as status_id
                    from orders join orders_order_status on orders.id = orders_order_status.order_id join order_status on order_status.id = orders_order_status.status_id
                    where $where
                    group by orders.id) as table1 
                    join order_status on table1.status_id = order_status.id and order_status.name = 'Đã giao'
                )as table2
                join order_detail on table2.id = order_detail.order_id join product_skus on product_skus.id = order_detail.sku_id join products on products.id = product_skus.product_id
            group by product_skus.id
            order by count(product_skus.id) desc
            limit 10
SQL;
            $table1 = DB::select($sql, $params);

            $sql = <<<SQL
            select count(product_skus.id) as count
            from
                (select table1.id
                 from
                 (select orders.id, max(order_status.id) as status_id
                  from orders join orders_order_status on orders.id = orders_order_status.order_id join order_status on order_status.id = orders_order_status.status_id
                  where $where
                  group by orders.id) as table1 
                 join order_status on table1.status_id = order_status.id and order_status.name = 'Đã giao'
                )as table2
                join order_detail on table2.id = order_detail.order_id join product_skus on product_skus.id = order_detail.sku_id join products on products.id = product_skus.product_id
SQL;
            $table2 = DB::select($sql, $params);
            $data = [ 'result' => $table1, 'total' => $table2[0]->count ];
        }

        return $data;
    }
}
