<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return true; // أو حسب الدور
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Report $report)
    {
        // المستخدم يمكنه رؤية التقرير إذا كان هو المالك أو المنشئ
        return $user->id === $report->user_id || $user->id === $report->created_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return true; // أو حسب الدور
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Report $report)
    {
        return $user->id === $report->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Report $report)
    {
        return $user->id === $report->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Report $report)
    {
        return $user->id === $report->created_by;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Report $report)
    {
        return $user->id === $report->created_by;
    }
}