<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelRolePermission extends Model
{
    use SoftDeletes;

    protected $table = 'rel_role_permissions';

    protected $fillable = [
        'id_role',
        'id_module',
        'id_permission',
    ];

    protected $casts = [
        'id' => 'integer',
        'id_role' => 'integer',
        'id_module' => 'integer',
        'id_permission' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(CatUser::class, 'id_role');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CatModule::class, 'id_module');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(CatPermission::class, 'id_permission');
    }
}
