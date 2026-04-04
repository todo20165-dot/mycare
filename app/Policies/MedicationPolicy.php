<?php

namespace App\Policies;

use App\Models\Medication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Medication $medication)
    {
        return $user->id === $medication->user_id;
    }

    public function create(User $user)
    {
        return $user->isPatient() || $user->isDoctor() || $user->isAdmin();
    }

    public function update(User $user, Medication $medication)
    {
        return $user->id === $medication->user_id;
    }

    public function delete(User $user, Medication $medication)
    {
        return $user->id === $medication->user_id;
    }
}
