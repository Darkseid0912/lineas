<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatPermission extends Model
{
    use SoftDeletes;
    protected $table = 'cat_permissions';

    protected $fillable = [
        'permission_name',
        'key',
        'id_companies',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'id_companies' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(CatCompany::class, 'id_companies');
    }
}
