<?php

namespace App\Traits;

use App\User;

trait AdminActions
{
    public function before(User $user, $ability){
        if ($user->esAdministrador()) {
            return true;
        }
    }
}
