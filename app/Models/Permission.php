<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends Model
{
    use HasFactory;
    protected $table = "permissions";
    protected $primaryKey = "permission_id";
    protected $keyType = "integer";
    protected $fillable = [
        'user_type',
        'user_id',
        'user_permission',
    ];
    public $incrementing = true;
    public $timestamps = true;
    public function user_groups(): BelongsTo
    {
        return $this->belongsTo(UserGroups::class, "user_group_id", "user_group_id");
    }
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }
}
