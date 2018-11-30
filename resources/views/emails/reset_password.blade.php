@component('mail::message')

<strong>EShop</strong>

@component('mail::button', ['url' => config('app.url').'/reset-password/'.$token])
Khôi phục mật khẩu
@endcomponent

@endcomponent
