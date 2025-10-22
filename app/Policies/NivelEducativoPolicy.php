<?php

namespace App\Policies;

use App\Models\User;
use App\Models\NivelEducativo;
use Illuminate\Auth\Access\Response;

class NivelEducativoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view niveles educativos');
    }

    public function view(User $user, NivelEducativo $nivel): bool
    {
        return $user->can('view niveles educativos');
    }

    public function create(User $user): bool
    {
        return $user->can('create niveles educativos');
    }

    public function update(User $user, NivelEducativo $nivel): bool
    {
        return $user->can('edit niveles educativos');
    }

    public function delete(User $user, NivelEducativo $nivel): bool
    {
        return $user->can('delete niveles educativos');
    }
}
