<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'height',
        'weight',
        'position',
        'number',
    ];

    public function getTeamNameAttribute()
    {
        return $this->player->team->name ?? 'Unknown';
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }
}
