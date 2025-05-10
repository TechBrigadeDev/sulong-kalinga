<?php
// app/Models/AppointmentType.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentType extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'appointment_type_id';
    
    protected $fillable = [
        'type_name',
        'color_code',
        'description'
    ];
    
    /**
     * Get the appointments of this type
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'appointment_type_id', 'appointment_type_id');
    }
}