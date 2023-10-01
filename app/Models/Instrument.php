<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instrument extends Model
{
    use HasFactory;

    protected $table = "instruments";
    protected $primaryKey = "instrument_id";
    protected $keyType = "integer";
    protected $fillable = [
        "code",
        "name",
        "brand",
        "status",
        "notes",
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];
    public $incrementing = true;
    public $timestamps = true;

    public function printers() : HasMany {
        return $this->hasMany(Printer::class, "instrument_id", "instrument_id");
    }
}
