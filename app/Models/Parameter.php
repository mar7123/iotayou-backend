<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parameter extends Model
{
    use HasFactory, HasUuids;

    protected $table = "parameters";
    protected $primaryKey = "parameter_id";
    protected $keyType = "string";
    protected $fillable = [
        "instrument_id",
        "code",
        "name",
        "status",
        "notes",
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];
    protected $appends = [
        'statuslang'
    ];
    public $incrementing = false;
    public $timestamps = true;

    public function printers(): BelongsToMany
    {
        return $this->belongsToMany(Printer::class, 'alarms', 'parameter_id', 'printer_id')
            ->using(Alarms::class)
            ->withPivot(["alarm_id", "name", "condition", "status", "notes", "occured_at", "solved_at"])
            ->withTimestamps();
    }
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, "status", "id");
    }
    public function instruments(): BelongsTo
    {
        return $this->belongsTo(Instrument::class, "instrument_id", "instrument_id");
    }
    public function alarms(): HasMany
    {
        return $this->hasMany(Alarm::class, 'parameter_id', 'parameter_id');
    }
    protected function getStatusLangAttribute()
    {
        $lang = $this->language()->first();
        return $lang->lang;
    }
}
