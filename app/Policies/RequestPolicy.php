<?php

namespace App\Policies;

use App\Models\User;

class RequestPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->role === 'supervisor';
    }
    
}
