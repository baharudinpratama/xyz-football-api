<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
        'year_founded',
        'address',
        'city',
        'is_internal',
    ];

    protected function logo(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? asset('storage/' . $this->logo) : asset('images/defaults/logo.png'),
        );
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(MatchSchedule::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(MatchSchedule::class, 'away_team_id');
    }
}
