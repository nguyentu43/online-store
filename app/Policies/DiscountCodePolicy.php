<?php

namespace App\Policies;

use App\User;
use App\DiscountCode;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiscountCodePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the discount code.
     *
     * @param  \App\User  $user
     * @param  \App\DiscountCode  $discountCode
     * @return mixed
     */

    public function before($user, $ability)
    {
        return in_array('all.manage', $user->permission) || in_array('discount-code.manage', $user->permission);
    }

    public function view(User $user, DiscountCode $discountCode)
    {
        return in_array('discount-code.read', $user->permission);
    }

    /**
     * Determine whether the user can create discount codes.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return in_array('discount-code.create', $user->permission);
    }

    /**
     * Determine whether the user can update the discount code.
     *
     * @param  \App\User  $user
     * @param  \App\DiscountCode  $discountCode
     * @return mixed
     */
    public function update(User $user, DiscountCode $discountCode)
    {
        return in_array('discount-code.update', $user->permission);
    }

    /**
     * Determine whether the user can delete the discount code.
     *
     * @param  \App\User  $user
     * @param  \App\DiscountCode  $discountCode
     * @return mixed
     */
    public function delete(User $user, DiscountCode $discountCode)
    {
        return in_array('discount-code.delete', $user->permission);
    }

    /**
     * Determine whether the user can restore the discount code.
     *
     * @param  \App\User  $user
     * @param  \App\DiscountCode  $discountCode
     * @return mixed
     */
    public function restore(User $user, DiscountCode $discountCode)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the discount code.
     *
     * @param  \App\User  $user
     * @param  \App\DiscountCode  $discountCode
     * @return mixed
     */
    public function forceDelete(User $user, DiscountCode $discountCode)
    {
        //
    }
}
