<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Printer extends Model
{
    use HasFactory;

    protected $table = "printers";
    protected $primaryKey = "printer_id";
    protected $keyType = "integer";
    protected $fillable = [
        "site_id",
        "code",
        "name",
        "instrument",
        "ip_addr",
        "printer_port",
        "image",
        "location",
        "coordinate",
        "status",
        "notes",
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];
    public $incrementing = true;
    public $timestamps = true;

    public function sites() : BelongsTo {
        return $this->belongsTo(Site::class, "site_id", "site_id");
    }
}
