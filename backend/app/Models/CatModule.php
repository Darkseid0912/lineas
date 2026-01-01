<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatModule extends Model
{
    use SoftDeletes;
    protected $table = 'cat_modules';

    protected $fillable = [
        'module_name',
        'description',
        'slug',
        'is_active',
        'id_companies',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'id_companies' => 'integer',
    ];

    // Eloquent manages timestamps by default; ensure created_at/updated_at work with DB schema

    public function company(): BelongsTo
    {
        return $this->belongsTo(CatCompany::class, 'id_companies');
    }
}
