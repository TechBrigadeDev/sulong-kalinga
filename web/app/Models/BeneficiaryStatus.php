<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryStatus extends Model
{
    use HasFactory;

    protected $table = 'beneficiary_status'; // Table name
    protected $primaryKey = 'beneficiary_status_id'; // Explicitly set the primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'status_name'
    ];
}