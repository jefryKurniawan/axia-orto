<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PatientAttachment;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'medical_record_number',
        'nik',
        'name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'province',
        'city',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'insurance_type',
        'insurance_number',
        'blood_type',
        'allergies',
        'medical_history',
        'is_active',
        'created_by', // Termasuk kolom audit jika Anda akan mengisinya dari Controller
        'updated_by',
    ];

    /**
     * Kolom yang harus di-cast ke tipe data tertentu.
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];


    // Kolom 'uuid' bersifat unik dan dibuat secara default di database,
    // Kita bisa memastikan Laravel tidak mencoba memasukkannya secara manual
    // saat operasi create, meskipun sudah ada di $fillable.

    // ----------------------------------------------------
    // RELATIONS (Diasumsikan ada Model User)
    // ----------------------------------------------------

    /**
     * Relasi ke User yang membuat data pasien.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang terakhir memperbarui data pasien.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function patientAttachments()
    {
        return $this->hasMany(PatientAttachment::class, 'patient_id');
    }
}
