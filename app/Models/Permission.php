<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Permission extends Pivot
{
    use HasFactory;
    protected $table = "permissions";
    protected $primaryKey = "permission_id";
    protected $keyType = "integer";
    protected $fillable = [
        'user_group',
        'user',
        'user_permission',
    ];
    protected $hidden = [
        'permission_id',
        'user_group',
        'user',
    ];
    public $incrementing = true;
    public $timestamps = true;
    public function user_group(): BelongsTo
    {
        return $this->belongsTo(UserGroups::class, "user_group", "user_group_id");
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user", "user_id");
    }
}
