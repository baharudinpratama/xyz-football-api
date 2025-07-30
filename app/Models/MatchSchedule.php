<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'match_date',
        'match_time',
        'home_team_id',
        'away_team_id',
    ];

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(MatchResult::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }
}
