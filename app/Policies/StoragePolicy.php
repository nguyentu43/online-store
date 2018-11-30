<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoragePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */

    public function store($user)
    {
        return in_array('storage.insert', $user->permission) || in_array('all.manage', $user->permission);
    }

    public function destroy($user)
    {
        return in_array('storage.delete', $user->permission) || in_array('all.manage', $user->permission);
    }
}
