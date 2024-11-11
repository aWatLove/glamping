<?php

namespace App\Policies;

use App\Models\Option;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OptionPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->role === 'user' || $user->role === 'manager' || $user->role === 'admin';
    }

    public function update(User $user)
    {
        return $user->role === 'manager' || $user->role === 'admin';
    }

    public function delete(User $user)
    {
        return $user->role === 'admin';
    }
}
