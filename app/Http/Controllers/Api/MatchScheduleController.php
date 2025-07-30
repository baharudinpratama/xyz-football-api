<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchSchedule;
use Illuminate\Http\Request;

class MatchScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MatchSchedule::query();

        if ($request->filled('date_from')) {
            $query->where('match_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('match_date', '<=', $request->date_to);
        }

        if ($request->filled('team_id')) {
            $teamId = $request->team_id;
            $query->where(function ($q) use ($teamId) {
                $q->where('home_team_id', $teamId)
                    ->orWhere('away_team_id', $teamId);
            });
        }

        if ($request->filled('has_result')) {
            $hasResult = filter_var($request->has_result, FILTER_VALIDATE_BOOLEAN);

            if ($hasResult) {
                $query->whereHas('result');
            } else {
                $query->doesntHave('result');
            }
        }

        if ($request->filled('include')) {
            $allowedIncludes = ['homeTeam', 'awayTeam', 'result', 'goals'];
            $includes = explode(',', $request->include);
            $validIncludes = array_intersect($includes, $allowedIncludes);

            if (!empty($validIncludes)) {
                $query->with($validIncludes);
            }
        }

        $matches = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Retrieve data success.',
            'data' => $matches,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_date' => 'required|date',
            'match_time' => 'required|date_format:H:i',
            'home_team_id' => 'required|exists:teams,id|different:away_team_id',
            'away_team_id' => 'required|exists:teams,id',
        ]);

        $match = MatchSchedule::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Match created successfully.',
            'data' => $match,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MatchSchedule $match)
    {
        $match->load(['homeTeam', 'awayTeam', 'result']);

        return response()->json([
            'status' => 'success',
            'message' => 'Retrieve data success.',
            'data' => $match,
        ]);
    }

    public function showMatchReport(MatchSchedule $match)
    {
        $match->load(['homeTeam', 'awayTeam', 'result']);

        $hasResult = $match->result !== null;

        $homeScore = $hasResult ? $match->result->home_score : null;
        $awayScore = $hasResult ? $match->result->away_score : null;

        $status = null;
        if ($hasResult) {
            if ($homeScore > $awayScore) {
                $status = 'Tim Home Menang';
            } elseif ($homeScore < $awayScore) {
                $status = 'Tim Away Menang';
            } else {
                $status = 'Draw';
            }
        }

        $goals = $match->goals()->with('player', 'player.team')->get();
        $topScorer = $goals->groupBy('player_id')->map->count()->sortDesc()->take(1);

        $topScorerData = null;
        if ($topScorer->isNotEmpty()) {
            $playerId = $topScorer->keys()->first();
            $goalCount = $topScorer->values()->first();
            $player = $goals->firstWhere('player_id', $playerId)->player;

            $topScorerData = [
                'name' => $player->name,
                'team' => $player->team->name ?? 'Unknown',
                'goals' => $goalCount
            ];
        }

        $matches = MatchSchedule::with('result')
            ->where(function ($q) use ($match) {
                $q->where('match_date', '<', $match->match_date)
                    ->orWhere(function ($q2) use ($match) {
                        $q2->where('match_date', $match->match_date)
                            ->where('match_time', '<=', $match->match_time);
                    });
            })
            ->get();

        $homeWins = $matches->filter(function ($m) use ($match) {
            $r = $m->result;
            if (!$r) return false;
            if ($m->home_team_id === $match->home_team_id && $r->home_score > $r->away_score) return true;
            if ($m->away_team_id === $match->home_team_id && $r->away_score > $r->home_score) return true;
            return false;
        })->count();

        $awayWins = $matches->filter(function ($m) use ($match) {
            $r = $m->result;
            if (!$r) return false;
            if ($m->home_team_id === $match->away_team_id && $r->home_score > $r->away_score) return true;
            if ($m->away_team_id === $match->away_team_id && $r->away_score > $r->home_score) return true;
            return false;
        })->count();

        return response()->json([
            'match_date' => $match->match_date,
            'match_time' => $match->match_time,
            'home_team' => $match->homeTeam->name,
            'away_team' => $match->awayTeam->name,
            'score' => "{$homeScore} - {$awayScore}",
            'result_status' => $status,
            'top_scorer' => $topScorerData,
            'home_team_wins_so_far' => $homeWins,
            'away_team_wins_so_far' => $awayWins
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MatchSchedule $match)
    {
        $validated = $request->validate([
            'match_date' => 'required|date',
            'match_time' => 'required|date_format:H:i',
            'home_team_id' => 'required|exists:teams,id|different:away_team_id',
            'away_team_id' => 'required|exists:teams,id',
        ]);

        $match->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Match updated successfully.',
            'data' => $match,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MatchSchedule $match)
    {
        $match->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Match deleted successfully.',
        ]);
    }
}
