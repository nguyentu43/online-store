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
            [ 'name' => 'Khách hàng', 'permission' => '["product.read", "comment.read", "rate.read", "comment.create", "rate.create", "comment.update.owner", "rate.update.owner", "comment.delete.owner", "rate.delete.owner", "order.create", "order.update", "user.update.owner"]']
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

        $category1 = App\Category::create([
            'name' => 'Phone',
            'order' => 1,
            'enable' => 1,
            'slug' => 'phone'
        ]);
        $category2 = App\Category::create([
            'name' => 'Tablet',
            'order' => 2,
            'enable' => 1,
            'slug' => 'tablet'
        ]);

        $brand1 = App\Brand::create([
            'name' => 'Apple',
            'slug' => 'apple'
        ]);
        $brand2 = App\Brand::create([
            'name' => 'Samsung',
            'slug' => 'samsung'
        ]);

        $ptype1 = App\ProductType::create([
            'name' => 'Phone'
        ]);
        $ptype2 = App\ProductType::create([
            'name' => 'Table'
        ]);

        $pattr1 = App\ProductAttribute::create([
            'name' => 'RAM'
        ]);
        $pattr2 = App\ProductAttribute::create([
            'name' => 'HDD'
        ]);
        $pattr3 = App\ProductAttribute::create([
            'name' => 'OS'
        ]);

        $ptype1->attributes()->sync([$pattr1->id, $pattr2->id, $pattr3->id]);
        $ptype2->attributes()->sync([$pattr1->id, $pattr2->id, $pattr3->id]);

        factory(App\Product::class, 10)->create()->each(function($p){

        	$p->brand()->associate(App\Brand::inRandomOrder()->first());
        	$p->type()->associate(App\ProductType::inRandomOrder()->first());
        	$p->category()->associate(App\Category::inRandomOrder()->first());
        	$p->save();

        	event(new App\Events\ProductEvent($p));

        });

        //$this->call(UsersTableSeeder::class);
    }
}
