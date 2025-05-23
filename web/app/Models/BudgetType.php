<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetType extends Model
{
    use HasFactory;

    protected $primaryKey = 'budget_type_id';
    
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Get the budget allocations for this type.
     */
    public function budgetAllocations()
    {
        return $this->hasMany(BudgetAllocation::class, 'budget_type_id');
    }
}