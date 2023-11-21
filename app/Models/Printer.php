<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Printer extends Model
{
    use HasFactory, HasUuids;

    protected $table = "printers";
    protected $primaryKey = "printer_id";
    protected $keyType = "string";
    protected $fillable = [
        "site_id",
        "instrument_id",
        "code",
        "name",
        "status",
        "notes",
    ];
    public $incrementing = false;
    public $timestamps = true;

    public function parameters(): BelongsToMany
    {
        return $this->belongsToMany(Parameter::class, 'alarms', 'printer_id', 'parameter_id')
            ->using(Alarms::class)
            ->withPivot(["alarm_id", "name", "condition", "status", "notes", "occured_at", "solved_at"])
            ->withTimestamps();
    }
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
