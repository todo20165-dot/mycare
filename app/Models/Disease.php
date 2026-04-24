<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialization',
        'description',
    ];

    /**
     * المرضى المصابون بهذا المرض
     */
    public function patients()
    {
        return $this->hasMany(User::class, 'disease_id')->where('role', 'patient');
    }
}
