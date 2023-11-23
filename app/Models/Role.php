<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, HasUuids;
    protected $table = "roles";
    protected $primaryKey = "role_id";
    protected $keyType = "string";
    protected $fillable = [
        'code',
        'name',
        'address',
        'status',
        'notes',
        'role_type',
        'parent_id',
        'deleted_at'
    ];
    protected $appends = [
        'statuslang',
    ];
    public $incrementing = false;
    public $timestamps = true;

    // many to one
    public function user_groups(): BelongsTo
    {
        return $this->belongsTo(UserGroups::class, "role_type", "user_group_id");
    }
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, "status", "id");
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'parent_id', 'role_id');
    }

    // one to many
    public function children(): HasMany
    {
        return $this->hasMany(Role::class, 'parent_id', 'role_id');
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_role_id', 'role_id');
    }
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class, 'customer_id', 'role_id');
    }
    protected function getStatusLangAttribute()
    {
        $lang = $this->language()->first();
        return $lang->lang;
    }
}
