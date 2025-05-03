<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;
    
    protected $table = 'barangays'; // Table name
    protected $primaryKey = 'barangay_id';
    protected $fillable = ['barangay_name', 'municipality_id'];
    
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'municipality_id');
    }
    
    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class, 'barangay_id', 'barangay_id');
    }
}