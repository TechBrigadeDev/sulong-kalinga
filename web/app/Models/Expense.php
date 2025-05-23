<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $primaryKey = 'expense_id';
    
    protected $fillable = [
        'title',
        'category_id',
        'amount',
        'payment_method',
        'date',
        'receipt_number',
        'description',
        'receipt_path',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Get the category for this expense.
     */
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    /**
     * Get the user who created this expense.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this expense.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}