<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;

class Site extends Model
{
    use HasFactory, HasUuids;

    protected $table = "sites";
    protected $primaryKey = "site_id";
    protected $keyType = "string";
    protected $fillable = [
        "customer_id",
        "code",
        "name",
        "address",
        "location",
        "status",
        "notes"
    ];
    protected $appends = [
        'statuslang',
    ];
    public $incrementing = false;
    public $timestamps = true;

    public function customers(): BelongsTo
    {
        return $this->belongsTo(Role::class, "customer_id", "role_id");
    }
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, "status", "id");
    }
    public function printers(): HasMany
    {
        return $this->hasMany(Printer::class, "site_id", "site_id");
    }
    protected function getStatusLangAttribute()
    {
        $lang = $this->language()->first();
        return $lang->lang;
    }
}
