<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\DoctorPatient;
use App\Models\FamilyLink;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Medication::class => \App\Policies\MedicationPolicy::class,
        \App\Models\VitalSign::class => \App\Policies\VitalSignPolicy::class,
        \App\Models\Report::class => \App\Policies\ReportPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('viewPatient', function ($user, $patient) {
            // المريض الشخصي يمكنه الاطلاع على نفسه
            if ($user->id === $patient->id) {
                return true;
            }

            // الأدمين يمكنه الاطلاع على أي مريض
            if ($user->isAdmin()) {
                return true;
            }

            // الطبيب يمكنه الاطلاع إذا كان مرتبطًا بالمريض وحالة الربط موافق عليها
            if ($user->isDoctor()) {
                return DoctorPatient::where('doctor_id', $user->id)
                    ->where('patient_id', $patient->id)
                    ->where('status', 'approved')
                    ->exists();
            }

            // فرد العائلة يمكنه الاطلاع إذا كان مرتبطًا بالمريض وحالة الربط موافق عليها
            if ($user->isFamilyMember()) {
                return FamilyLink::where('patient_id', $patient->id)
                    ->where('family_member_id', $user->id)
                    ->where('status', 'approved')
                    ->exists();
            }

            return false;
        });
    }
}
