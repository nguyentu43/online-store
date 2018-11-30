<?php

namespace App\Policies;

use App\User;
use App\ProductAttribute;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductAttributePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the product attribute.
     *
     * @param  \App\User  $user
     * @param  \App\ProductAttribute  $productAttribute
     * @return mixed
     */

    public function before($user, $ability)
    {
        return in_array('all.manage', $user->permission) || in_array('productattr.manage', $user->permission);
    }

    public function view(User $user, ProductAttribute $productAttribute)
    {
        return in_array('productattr.read', $user->permission);
    }

    /**
     * Determine whether the user can create product attributes.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return in_array('productattr.create', $user->permission);
    }

    /**
     * Determine whether the user can update the product attribute.
     *
     * @param  \App\User  $user
     * @param  \App\ProductAttribute  $productAttribute
     * @return mixed
     */
    public function update(User $user, ProductAttribute $productAttribute)
    {
        return in_array('productattr.update', $user->permission);
    }

    /**
     * Determine whether the user can delete the product attribute.
     *
     * @param  \App\User  $user
     * @param  \App\ProductAttribute  $productAttribute
     * @return mixed
     */
    public function delete(User $user, ProductAttribute $productAttribute)
    {
        return in_array('productattr.delete', $user->permission);
    }

    /**
     * Determine whether the user can restore the product attribute.
     *
     * @param  \App\User  $user
     * @param  \App\ProductAttribute  $productAttribute
     * @return mixed
     */
    public function restore(User $user, ProductAttribute $productAttribute)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the product attribute.
     *
     * @param  \App\User  $user
     * @param  \App\ProductAttribute  $productAttribute
     * @return mixed
     */
    public function forceDelete(User $user, ProductAttribute $productAttribute)
    {
        //
    }
}
