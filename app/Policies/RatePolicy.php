<?php

namespace App\Policies;

use App\User;
use App\Rate;
use Illuminate\Auth\Access\HandlesAuthorization;

class RatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the rate product.
     *
     * @param  \App\User  $user
     * @param  \App\RateProduct  $rateProduct
     * @return mixed
     */

    public function before($user, $ability)
    {
        return in_array('all.manage', $user->permission) || in_array('rate.manage', $user->permission);
    }

    public function view(User $user, Rate $rate)
    {
        return true;
    }

    /**
     * Determine whether the user can create rate products.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return in_array('rate.create', $user->permission);
    }

    /**
     * Determine whether the user can update the rate product.
     *
     * @param  \App\User  $user
     * @param  \App\RateProduct  $rateProduct
     * @return mixed
     */
    public function update(User $user, Rate $rate)
    {
        return in_array('rate.update.owner', $user->permission) && $rate->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the rate product.
     *
     * @param  \App\User  $user
     * @param  \App\RateProduct  $rateProduct
     * @return mixed
     */
    public function delete(User $user, Rate $rate)
    {
        return in_array('rate.delete.owner', $user->permission) && $rate->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the rate product.
     *
     * @param  \App\User  $user
     * @param  \App\RateProduct  $rateProduct
     * @return mixed
     */
    public function restore(User $user, Rate $rate)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the rate product.
     *
     * @param  \App\User  $user
     * @param  \App\RateProduct  $rateProduct
     * @return mixed
     */
    public function forceDelete(User $user, Rate $rate)
    {
        //
    }
}
