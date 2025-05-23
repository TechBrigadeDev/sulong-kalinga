<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';
    
    protected $fillable = [
        'name',
        'color_code',
        'icon'
    ];

    /**
     * Get the expenses for this category.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}