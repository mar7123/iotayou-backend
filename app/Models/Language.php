<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    protected $table = "languages";
    protected $primaryKey = "id";
    protected $keyType = "integer";
    protected $fillable = [
        'langfunction',
        'lang',
        'badge',
    ];
    public $incrementing = true;
    public $timestamps = true;
}
