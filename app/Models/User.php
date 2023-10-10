<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
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
        'first_name',
        'last_name',
        'email',
        'phone_num',
        'picture',
        'salt',
        'password',
        'reset_token',
        'address',
        'birth_date',
        'join_date',
        'plant',
        'site',
        'dash_suffix',
        'sendalmsms',
        'sendalmemail',
        'sendreport',
        'announcement',
        'user_type',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public $incrementing = false;
    public $timestamps = true;

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_children', 'parent_id', 'child_id')->using(ParentChild::class);
    }
    public function parent(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_children', 'child_id', 'parent_id')->using(ParentChild::class);
    }
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class, 'customer_id', 'user_id');
    }
}
