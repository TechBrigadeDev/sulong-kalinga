<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterventionTagalog extends Model
{
    protected $table = 'interventions_tagalog';
    protected $primaryKey = 't_intervention_id';
    public $timestamps = false;

    protected $fillable = [
        'care_category_id',
        't_intervention_description',
    ];

    // Optionally, add a relationship to CareCategory
    public function careCategory()
    {
        return $this->belongsTo(CareCategory::class, 'care_category_id', 'care_category_id');
    }
}
