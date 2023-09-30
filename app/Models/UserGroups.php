<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
