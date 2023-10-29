<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alarm extends Model
{
    use HasFactory;

    protected $table = "alarms";
    protected $primaryKey = "alarm_id";
    protected $keyType = "integer";
    protected $fillable = [
        "printer_id",
        "parameter_id",
        "name",
        "condition",
        "status",
        "notes",
        "occured_at",
        "solved_at"
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];
    protected $appends = [
        "duration"
    ];
    public $incrementing = true;
    public $timestamps = true;

    public function printers(): BelongsTo
    {
        return $this->belongsTo(Printer::class, "printer_id", "printer_id");
    }
    public function parameters(): BelongsTo
    {
        return $this->belongsTo(Parameter::class, "parameter_id", "parameter_id");
    }
    public function getDurationAttribute(): string
    {
        if ($this->solved_at == null) {
            $time = new DateTime();
            $timenow = $time->getTimestamp();
            $diff =  $timenow - strtotime($this->occured_at);
            return ($diff / 60) . ' minutes';
        } else {
            $diff = strtotime($this->solved_at) - strtotime($this->occured_at);
            return ($diff / 60) . ' minutes';
        }
    }
}
