<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    protected $table = 'discount_code';

    protected $fillable = [ 'code', 'begin_date', 'end_date', 'count', 'value' ];

    protected $casts = [
    	'value' => 'object'
    ];

    public function check($user_id, $items, $order = false)
    {
        $condition = $this->value;

        if(!empty($this->count) && $this->count == 0)
        {
            goto invalid;
        }

        if(!empty($condition->users) && !in_array($user_id, $condition->users))
        {
            goto invalid;
        }

        $amount_valid = 0;
        $amount = 0;

        foreach ($items as $item) {

            $sku = ProductSku::find($item['sku_id']);

            $product = $sku->product;

            $discount = $sku->discount && $sku->discount->is_expire ? $sku->discount->value : 0;
            $item_amount = $sku->price * (1 - $discount) * $item['quantity'];
            $amount += $item_amount;

            if(!empty($condition->categories) && in_array($product->category->id, $condition->categories))
            {
                $amount_valid += $item_amount;
            }

            if(!empty($condition->ptype) && in_array($product->product_type_id, $condition->ptype))
            {
                $amount_valid += $item_amount;
            }

            if(!empty($condition->brand) && in_array($product->brand_id, $condition->brand))
            {
                $amount_valid += $item_amount;
            }

            if(!empty($condition->products))
            {
                if(count(array_filter($condition->products, function($p){

                    $p->sku_id == $sku->id;

                })) > 1)
                {
                    $amount_valid += $item_amount;
                }
            }

            if(!empty($condition->tags) && strstr($product->name, $condition->tags))
            {
                $amount_valid += $item_amount;
            }
            
        }

        if(!empty($condition->minOrder))
        {
            if(!empty($condition->tags) || !empty($condition->products) || !empty($condition->categories) || !empty($condition->ptype) || !empty($condition->brand))
            {
                if($condition->minOrder > $amount_valid)
                {
                    goto invalid;
                }
                else
                {
                    $amount = $amount_valid;
                }
            }
            else
            {
                if($condition->minOrder > $amount)
                {
                    goto invalid;
                }
            }
        }

        $discount = $this->value->discount;

        if($this->value->type == 0)
        {
            $discount = $amount * $discount;

            if(!empty($condition->max))
            {
                $discount = min($condition->max, $discount);
            }
        }

        if($order && !empty($this->count))
        {
        	$this->count--;
        	$this->save();
        }

        return [
            'status' => 'ok',
            'discount' => $discount
        ];

        invalid:
        return [
            'status' => 'error',
            'message' => 'Mã giảm giá không hợp lệ'
        ];
    }
}
