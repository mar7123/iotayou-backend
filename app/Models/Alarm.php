<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Alarm extends Pivot
{
    use HasFactory, HasUuids;

    protected $table = "alarms";
    protected $primaryKey = "alarm_id";
    protected $keyType = "string";
    protected $fillable = [
        "printer_id",
        "parameter_id",
        "name",
        "condition",
        "status",
        "notes",
        // "occured_at",
        // "solved_at"
    ];
    protected $appends = [
        'statuslang',
        // "duration"
    ];
    public $incrementing = false;
    public $timestamps = true;

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, "status", "id");
    }
    public function printers(): BelongsTo
    {
        return $this->belongsTo(Printer::class, "printer_id", "printer_id");
    }
    public function parameters(): BelongsTo
    {
        return $this->belongsTo(Parameter::class, "parameter_id", "parameter_id");
    }
    // public function getDurationAttribute(): string
    // {
    //     if ($this->solved_at == null) {
    //         $time = new DateTime();
    //         $timenow = $time->getTimestamp();
    //         $diff =  $timenow - strtotime($this->occured_at);
    //         return ($diff / 60) . ' minutes';
    //     } else {
    //         $diff = strtotime($this->solved_at) - strtotime($this->occured_at);
    //         return ($diff / 60) . ' minutes';
    //     }
    // }
    protected function getStatusLangAttribute()
    {
        $lang = $this->language()->first();
        return $lang->lang;
    }
}
