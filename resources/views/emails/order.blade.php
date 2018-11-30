@component('mail::message', [ 'order' => $order ])
<strong>EShop</strong>

<h4>Thông tin đơn hàng #{{ $order->order_code }}</h4>
<h4>Tình trạng hiện tại: {{ $order->status->last()->name }} lúc {{ $order->status->last()->pivot->created_at }}</h4>

@component('mail::panel', [ 'order' => $order ])
<strong>Người đặt hàng</strong>
<ul>
	<li>Họ và tên: {{ $order->user->name }}</li>
	<li>Địa chỉ email: {{ $order->user->email }}</li>
</ul>
@endcomponent

<br/>

@component('mail::panel', [ 'order' => $order ])
<strong>Người nhận</strong>
<ul>
	<li>Họ và tên: {{ $order->customer_name }}</li>
	<li>Địa chỉ: {{ $order->address }} {{ $order->city }}</li>
	<li>Số ĐT: {{ $order->phone }}</li>
	<li>Ghi chú: {{ empty($order->description) ? 'Không có' : $order->description }}</li>
</ul>
@endcomponent

<br/>

@component('mail::table', [ 'order' => $order ])

| #	| Tên sản phẩm | Số lượng | Giá tiền |
| - | :----------: | :------: | :------: |
@foreach($order->items as $item)
| {{ $loop->index + 1 }} | {{ $item->product->name }} | {{ $item->pivot->quantity }} | {{ number_format($item->pivot->quantity * ( 1 - $item->pivot->discount) * $item->pivot->price) }}đ |
@endforeach

@endcomponent

@if(!empty($order->discount))
<h4>Đã giảm: {{ number_format($order->discount->value) }}đ</h4>
<h4><del>{{ number_format($order->discount->value + $order->amount) }}</del>đ</h4>
@endif
<h4>Tổng giá trị đơn hàng: {{ number_format($order->amount) }}đ</h4>

<br/>
@endcomponent
