<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetAllocation extends Model
{
    use HasFactory;

    protected $primaryKey = 'budget_allocation_id';
    
    protected $fillable = [
        'amount',
        'start_date',
        'end_date',
        'budget_type_id',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Get the budget type for this allocation.
     */
    public function budgetType()
    {
        return $this->belongsTo(BudgetType::class, 'budget_type_id');
    }

    /**
     * Get the user who created this budget allocation.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this budget allocation.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}