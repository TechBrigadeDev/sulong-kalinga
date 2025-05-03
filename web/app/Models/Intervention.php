<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    use HasFactory;

    protected $table = 'interventions'; // Table name
    protected $primaryKey = 'intervention_id'; // Explicitly set the primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'care_category_id', 'intervention_description'
    ];

    public function careCategory()
    {
        return $this->belongsTo(CareCategory::class, 'care_category_id', 'care_category_id');
    }
}