<?php

namespace App\Models;

use Carbon\CarbonInterface;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasFactory;
    protected $table = "alerts";
    protected $primaryKey = "alert_id";
    protected $keyType = "integer";
    protected $fillable = [
        "code",
        "name",
        "site_name",
        "printer_name",
        "status",
        "occured_at",
        "solved_at",
    ];
    protected $appends = [
        'duration',
        'statuslang'
    ];
    protected $casts = [
        'occured_at' => 'datetime',
        'solved_at' => 'datetime',
    ];
    public $incrementing = true;
    public $timestamps = true;

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, "status", "id");
    }
    public function getDurationAttribute(): string
    {
        if ($this->solved_at == null) {
            // $time = new DateTime();
            // $timenow = $time->getTimestamp();
            // $diff =  $timenow - strtotime($this->occured_at);
            // return ($diff / 60) . ' minutes';
            return $this->occured_at->diffForHumans(now(), CarbonInterface::DIFF_ABSOLUTE, true, 3);
        } else {
            // $diff = strtotime($this->solved_at) - strtotime($this->occured_at);
            // return ($diff / 60) . ' minutes';
            return $this->occured_at->diffForHumans($this->solved_at, CarbonInterface::DIFF_ABSOLUTE, true, 3);
        }
    }
    protected function getStatusLangAttribute()
    {
        $lang = $this->language()->first();
        return $lang->lang;
    }
}
