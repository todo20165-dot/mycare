<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VitalSign;
use Illuminate\Auth\Access\HandlesAuthorization;

class VitalSignPolicy
{
    use HandlesAuthorization;

    public function view(User $user, VitalSign $vitalSign)
    {
        return $user->id === $vitalSign->user_id;
    }

    public function create(User $user)
    {
        return $user->isPatient() || $user->isDoctor() || $user->isAdmin();
    }

    public function update(User $user, VitalSign $vitalSign)
    {
        return $user->id === $vitalSign->user_id;
    }

    public function delete(User $user, VitalSign $vitalSign)
    {
        return $user->id === $vitalSign->user_id;
    }
}
