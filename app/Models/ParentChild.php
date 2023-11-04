<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ParentChild extends Pivot
{
    use HasFactory;
    protected $table = "parent_children";
    protected $primaryKey = "parent_children_id";
    protected $keyType = "integer";
    protected $fillable = [
        'parent_id',
        'child_id',
    ];
    protected $hidden = [
        "parent_children_id"
    ];
    public $incrementing = true;
    public $timestamps = true;
}
