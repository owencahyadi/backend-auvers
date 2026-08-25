<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToStore;
    protected $guarded = [];

    public function dailyRate() {
        return $this->hasOne(DailyRate::class);
    }

    public function rosters() {
        return $this->hasMany(Roster::class);
    }
}
