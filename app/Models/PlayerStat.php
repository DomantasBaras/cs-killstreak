<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerStat extends Model
{
    protected $fillable = [
        'ip', 'name', 'kills', 'deaths', 'headshots', 'playtime', 'last_seen',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    protected $appends = ['kd_ratio'];

    public function getKdRatioAttribute(): float
    {
        if ($this->deaths === 0) return (float) $this->kills;
        return round($this->kills / $this->deaths, 2);
    }
}