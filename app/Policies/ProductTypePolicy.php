<?php

namespace App\Policies;

use App\User;
use App\ProductType;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the product type.
     *
     * @param  \App\User  $user
     * @param  \App\ProductType  $productType
     * @return mixed
     */

    public function before($user, $ability)
    {
        return in_array('all.manage', $user->permission) || in_array('producttype.manage', $user->permission);
    }

    public function view(User $user, ProductType $productType)
    {
        return in_array('producttype.read', $user->permission);
    }

    /**
     * Determine whether the user can create product types.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return in_array('producttype.create', $user->permission);
    }

    /**
     * Determine whether the user can update the product type.
     *
     * @param  \App\User  $user
     * @param  \App\ProductType  $productType
     * @return mixed
     */
    public function update(User $user, ProductType $productType)
    {
        return in_array('producttype.update', $user->permission);
    }

    /**
     * Determine whether the user can delete the product type.
     *
     * @param  \App\User  $user
     * @param  \App\ProductType  $productType
     * @return mixed
     */
    public function delete(User $user, ProductType $productType)
    {
        return in_array('producttype.delete', $user->permission);
    }

    /**
     * Determine whether the user can restore the product type.
     *
     * @param  \App\User  $user
     * @param  \App\ProductType  $productType
     * @return mixed
     */
    public function restore(User $user, ProductType $productType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the product type.
     *
     * @param  \App\User  $user
     * @param  \App\ProductType  $productType
     * @return mixed
     */
    public function forceDelete(User $user, ProductType $productType)
    {
        //
    }
}
