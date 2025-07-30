<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\MatchSchedule;
use App\Models\Player;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function indexByMatch(MatchSchedule $match)
    {
        $goals = Goal::with(['player', 'player.team'])
            ->where('match_schedule_id', $match->id)
            ->orderBy('minute')
            ->get();

        return response()->json([
            'match_id' => $match->id,
            'total_goals' => $goals->count(),
            'goals' => $goals->map(function ($goal) {
                return [
                    'id' => $goal->id,
                    'minute' => $goal->minute,
                    'player' => [
                        'id' => $goal->player->id,
                        'name' => $goal->player->name,
                        'team_id' => $goal->player->team_id,
                        'team_name' => $goal->player->team->name ?? 'Unknown',
                    ],
                    'is_own_goal' => $goal->is_own_goal ?? false,
                ];
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MatchSchedule $match)
    {
        $validated = $request->validate([
            'team_id' => [
                'required',
                'exists:teams,id',
                function ($attribute, $value, $fail) use ($match) {
                    if ($value != $match->home_team_id && $value != $match->away_team_id) {
                        $fail('Team is not part of the match.');
                    }
                },
            ],
            'player_id' => 'required|exists:players,id',
            'minute' => 'required|integer|min:1|max:120',
            'is_own_goal' => 'nullable|boolean',
        ]);

        $player = Player::find($validated['player_id']);
        if ($player->team_id !== intval($validated['team_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Player not part of the team.',
            ], 422);
        }

        $goal = Goal::create([
            'match_schedule_id' => $match->id,
            'team_id' => $validated['team_id'],
            'player_id' => $validated['player_id'],
            'minute' => $validated['minute'],
            'is_own_goal' => $validated['is_own_goal'] ?? false,
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'Goal created successfully.',
            'data' => $goal,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Goal $goal)
    {
        $goal->load('match');

        $validated = $request->validate([
            'team_id' => [
                'required',
                'exists:teams,id',
                function ($attribute, $value, $fail) use ($goal) {
                    $match = $goal->match;
                    if ($value != $match->home_team_id && $value != $match->away_team_id) {
                        $fail('Team is not part of the match.');
                    }
                },
            ],
            'player_id' => 'required|exists:players,id',
            'minute' => 'required|integer|min:1|max:120',
            'is_own_goal' => 'nullable|boolean',
        ]);

        $player = Player::find($validated['player_id']);
        if ($player->team_id !== intval($validated['team_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Player not part of the team.',
            ], 422);
        }

        $goal->update([
            'team_id' => $validated['team_id'],
            'player_id' => $validated['player_id'],
            'minute' => $validated['minute'],
            'is_own_goal' => $validated['is_own_goal'] ?? false,
        ]);

        $goal->load('player');

        return response()->json([
            'status' => 'success',
            'message' => 'Goal updated successfully.',
            'data' => $goal,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Goal $goal)
    {
        $goal->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Goal deleted successfully.',
        ]);
    }
}
