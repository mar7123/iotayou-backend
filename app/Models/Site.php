<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Site extends Model
{
    use HasFactory, LogsActivity, HasUuids;

    protected $table = "sites";
    protected $primaryKey = "site_id";
    protected $keyType = "string";
    protected $fillable = [
        "customer_id",
        "code",
        "name",
        "address",
        "sourceloc",
        "location",
        "pic",
        "status",
        "notes"
    ];
    protected $hidden = [
        "sourceloc",
        "pic",
        "created_at",
        "updated_at",
    ];
    public $incrementing = false;
    public $timestamps = true;

    public function customers(): BelongsTo
    {
        return $this->belongsTo(User::class, "customer_id", "user_id");
    }
    public function printers(): HasMany
    {
        return $this->hasMany(Printer::class, "site_id", "site_id");
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['site_id', 'code', 'name']);
    }
}
