<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    public function canUpdate()
    {
        return auth()->user()->hasPermission('update_role');
    }

    public function canRead()
    {
        return auth()->user()->hasPermission('read_role');
    }

    public function canCreate()
    {
        return auth()->user()->hasPermission('create_role');
    }

    public function canDelete()
    {
        return auth()->user()->hasPermission('delete_role');
    }
}
