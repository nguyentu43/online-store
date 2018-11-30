<?php

namespace App\Listeners;

use App\Events\ProductEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateProductSkuListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ProductEvent  $event
     * @return void
     */
    public function handle(ProductEvent $event)
    {
        $product = $event->product;

        $brand_name = mb_substr($product->brand->name, 0, 3);
        $arr = explode(' ', $product->type->name);
        $product_type_name = array_reduce($arr, function ($carry, $value){ $carry .= mb_substr($value, 0, 1); return $carry; });
        $id = str_pad($product->id, 6, 0, STR_PAD_LEFT);

        $sku = strtoupper(iconv('utf-8', 'ascii//translit', $brand_name.$product_type_name.$id));

        $product->skus()->create([
            'sku' => $sku,
            'price' => 1000,
            'quantity' => 1,
            'name' => 'DEFAULT'
        ]);
    }
}
