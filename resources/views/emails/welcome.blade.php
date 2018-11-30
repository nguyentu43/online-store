@component('mail::message', [ 'user' => $user ])
<strong>EShop</strong>

Xin chào {{ $user->name }} đã đăng ký tài khoản thành công

@component('mail::button', ['url' => config('app.url')])
Mua ngay
@endcomponent

@endcomponent
