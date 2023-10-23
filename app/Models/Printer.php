<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Printer extends Model
{
    use HasFactory;

    protected $table = "printers";
    protected $primaryKey = "printer_id";
    protected $keyType = "integer";
    protected $fillable = [
        "site_id",
        "instrument_id",
        "code",
        "name",
        "ip_addr",
        "printer_port",
        "image",
        "location",
        "coordinate",
        "status",
        "notes",
    ];
    protected $hidden = [
        "ip_addr",
        "printer_port",
        "image",
        "location",
        "coordinate",
        "created_at",
        "updated_at",
    ];
    public $incrementing = true;
    public $timestamps = true;

    public function sites(): BelongsTo
    {
        return $this->belongsTo(Site::class, "site_id", "site_id");
    }
    public function instruments(): BelongsTo
    {
        return $this->belongsTo(Instrument::class, "instrument_id", "instrument_id");
    }
    public function alarms(): HasMany
    {
        return $this->hasMany(Alarm::class, 'printer_id', 'printer_id');
    }
}
