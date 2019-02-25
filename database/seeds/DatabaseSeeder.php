<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	//add payment_method

        DB::table('payment_methods')->insert([
            [ 'name' => 'COD' ],
            [ 'name' => 'Thanh toán trực tuyến Stripe']
        ]);

        DB::table('login_providers')->insert([
            [ 'name' => 'email' ],
            [ 'name' => 'google'],
            [ 'name' => 'facebook'],
        ]);

        DB::table('roles')->insert([
            [ 'name' => 'Quản trị', 'permission' => '[ "all.manage" ]'],
            [ 'name' => 'Khách hàng', 'permission' => '["product.read", "comment.read", "rate.read", "comment.create", "rate.create", "comment.update.owner", "rate.update.owner", "comment.delete.owner", "rate.delete.owner", "order.create", "order.update"]']
        ]);

        $user = factory(App\User::class)->create([
            'email' => 'eshop@eshop.test',
            'password' => Hash::make('123456'),
            'name' => 'admin_eshop'
        ]);

        $user->roles()->attach(App\Role::where('name', 'Quản trị')->first()->id);
        $user->login_providers()->attach(App\LoginProvider::where('name', 'email')->first()->id);
        $user->cart()->create([]);
        $user->update([ 'enable' => true ]);

        DB::table('order_status')->insert([
            [ 'name' => 'Đã tiếp nhận' ],
            [ 'name' => 'Đang xử lí'],
            [ 'name' => 'Đang giao'],
            [ 'name' => 'Đã giao'],
            [ 'name' => 'Hoàn trả'],
            [ 'name' => 'Đã huỷ']
        ]);

        //$this->call(UsersTableSeeder::class);
    }
}
