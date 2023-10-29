<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parameter extends Model
{
    use HasFactory;

    protected $table = "parameters";
    protected $primaryKey = "parameter_id";
    protected $keyType = "integer";
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
    public $incrementing = true;
    public $timestamps = true;

    public function instruments(): BelongsTo
    {
        return $this->belongsTo(Instrument::class, "instrument_id", "instrument_id");
    }
    public function alarms(): HasMany
    {
        return $this->hasMany(Alarm::class, 'parameter_id', 'parameter_id');
    }
}