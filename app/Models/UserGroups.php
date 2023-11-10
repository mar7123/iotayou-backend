<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserGroups extends Model
{
    use HasFactory;
    protected $table = "user_groups";
    protected $primaryKey = "user_group_id";
    protected $keyType = "integer";
    protected $fillable = [
        'name',
        'icon',
        'page1st',
        'group_code',
    ];
    protected $hidden = [
        'icon',
        'page1st',
        'group_code',
        'created_at',
        'updated_at'
    ];
    public $incrementing = true;
    public $timestamps = true;

    public function user_permissions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permissions', 'user_group_id', 'user_id')
            ->using(Permission::class)
            ->withPivot(['permission_id', 'user_permission'])
            ->withTimestamps();
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_type', 'user_group_id');
    }
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'user_group_id', 'user_group_id');
    }
}
