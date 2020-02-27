<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Intel extends Model
{
    use Notifiable;
    protected $table = "intel";

    protected $fillable = [
        "country",
        "state",
        "confirmed",
        "deaths",
        "recovered",
        "suspected",
        "source"
    ];

    public function scopeActive($query)
    {
        return $query->whereRaw('(`confirmed` - `recovered` - `deaths`) > 0');
    }

    public function getActiveInfectionsAttribute() {
        return $this->confirmed - $this->recovered - $this->death;
    }

    public function routeNotificationForSlack() {
        return env('SLACK_WEBHOOK_URL');
    }
}
