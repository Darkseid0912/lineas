<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatCompany extends Model
{
    use SoftDeletes;

    protected $table = 'cat_companies';

    protected $fillable = [
        'tax_id',
        'business_name',
        'trade_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
