<?php

namespace App\Policies;

use App\Models\Option;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function show(User $user)
    {
        return $user->role === 'admin' || $user->role === 'user';
    }

    public function create(User $user)
    {
        return $user->role === 'admin' || $user->role === 'user';
    }

    public function update(User $user)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user)
    {
        return $user->role === 'admin';
    }

    public function adminRules(User $user)
    {
        return $user->role === 'admin';
    }
}
