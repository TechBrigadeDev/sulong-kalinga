<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareCategory extends Model
{
    use HasFactory;

    protected $table = 'care_categories'; // Table name
    protected $primaryKey = 'care_category_id'; // Explicitly set the primary key
    public $timestamps = false; // Disable timestamps

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'care_category_name'
    ];

    public function careNeeds()
    {
        return $this->belongsTo(CareNeed::class, 'care_category_id');
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'care_category_id', 'care_category_id');
    }

}