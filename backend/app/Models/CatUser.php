<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatUser extends Model
{
    use SoftDeletes;

    protected $table = 'cat_users';

    protected $fillable = [
        'name',
        'id_companies',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'id_companies' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(CatCompany::class, 'id_companies');
    }
}
