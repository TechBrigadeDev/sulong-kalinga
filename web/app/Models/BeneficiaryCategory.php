<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryCategory extends Model
{
    use HasFactory;

    protected $table = 'beneficiary_categories'; // Table name
    protected $primaryKey = 'category_id'; // Explicitly set the primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category_name'
    ];
}