<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Product::class, 10)->create()->each(function($p){

        	$p->brand()->associate(App\Brand::inRandomOrder()->first());
        	$p->type()->associate(App\ProductType::inRandomOrder()->first());
        	$p->category()->associate(App\Category::inRandomOrder()->first());
        	$p->save();

        	event(new App\Events\ProductEvent($p));

        });
    }
}
