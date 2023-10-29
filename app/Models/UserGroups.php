<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    public $incrementing = true;
    public $timestamps = true;
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_type', 'user_group_id');
    }
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'user_group_id', 'user_group_id');
    }
}
