<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    public function isAdmin()
    {
        return auth()->user()->hasRole("admin");
    }

    public function canUpdate()
    {
        return auth()->user()->hasPermission('update_user');
    }

    public function canRead()
    {
        return auth()->user()->hasPermission('read_user');
    }

    public function canCreate()
    {
        return auth()->user()->hasPermission('create_user');
    }

    public function canDelete()
    {
        return auth()->user()->hasPermission('delete_user');
    }

    public function loggedUser($uuid)
    {
        return auth()->user()->uuid == $uuid;
    }
}
