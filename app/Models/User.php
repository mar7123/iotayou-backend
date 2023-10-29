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
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, LogsActivity;

    protected $table = "users";
    protected $primaryKey = "user_id";
    protected $keyType = "string";
    protected $fillable = [
        'code',
        'full_name',
        'email',
        'phone_num',
        'pic',
        'address',
        'salt',
        'password',
        'picture',
        'reset_token',
        'birth_date',
        'join_date',
        'plant',
        'site',
        'dash_suffix',
        'sendalmsms',
        'sendalmemail',
        'sendreport',
        'announcement',
        'email_verified_at',
        'user_type',
        'status',
        'notes',
    ];
    protected $hidden = [
        'phone_num',
        'pic',
        'salt',
        'password',
        'picture',
        'reset_token',
        'birth_date',
        'join_date',
        'plant',
        'site',
        'dash_suffix',
        'sendalmsms',
        'sendalmemail',
        'sendreport',
        'announcement',
        'email_verified_at',
        'user_type',
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
        return $this->belongsToMany(User::class, 'parent_children', 'parent_id', 'child_id')
        ->using(ParentChild::class)
        ->withPivot(['parent_children_id'])
        ->withTimestamps();
    }
    public function parent(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_children', 'child_id', 'parent_id')
        ->using(ParentChild::class)
        ->withPivot(['parent_children_id'])
        ->withTimestamps();
    }
    public function user_groups(): BelongsTo
    {
        return $this->belongsTo(UserGroups::class, "user_type", "user_group_id");
    }
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class, 'customer_id', 'user_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['email']);
    }
}
