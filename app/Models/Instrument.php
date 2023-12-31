<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instrument extends Model
{
    use HasFactory, HasUuids;

    protected $table = "instruments";
    protected $primaryKey = "instrument_id";
    protected $keyType = "string";
    protected $fillable = [
        "code",
        "name",
        "status",
        "notes",
    ];
    protected $hidden = [
        "brand",
        "created_at",
        "updated_at",
    ];
    protected $appends = [
        'statuslang'
    ];
    public $incrementing = false;
    public $timestamps = true;

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, "status", "id");
    }

    public function printers(): HasMany
    {
        return $this->hasMany(Printer::class, "instrument_id", "instrument_id");
    }
    public function parameters(): HasMany
    {
        return $this->hasMany(Parameter::class, "instrument_id", "instrument_id");
    }
    protected function getStatusLangAttribute()
    {
        $lang = $this->language()->first();
        return $lang->lang;
    }
}
