<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $table = "users";
    protected $primaryKey = "user_id";
    protected $keyType = "string";
    protected $fillable = [
        'username',
        'email',
        'name',
        'phone_num',
        'salt',
        'password',
        'status',
        'notes',
        'deleted_at',
        'user_role_id',
        'email_verified_at',
        'picture'
    ];
    protected $hidden = [
        'salt',
        'password',
        'status'
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $appends = [
        'user_type',
        'statuslang',
    ];
    public $incrementing = false;
    public $timestamps = true;

    // many to many
    public function user_permissions(): BelongsToMany
    {
        return $this->belongsToMany(UserGroups::class, 'permissions', 'user', 'user_group')
            ->using(Permission::class)
            ->withPivot(['permission_id', 'user_permission'])
            ->withTimestamps();
    }

    // one to many
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'user', 'user_id');
    }

    // many to one
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, "user_role_id", "role_id");
    }
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, "status", "id");
    }

    // appended attribute
    protected function getUserTypeAttribute()
    {
        $type = $this->role()->first()->user_groups()->first();
        return $type->name;
    }
    protected function getStatusLangAttribute()
    {
        $lang = $this->language()->first();
        return $lang->lang;
    }
}
