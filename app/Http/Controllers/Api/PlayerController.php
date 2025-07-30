<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Player::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        if ($request->filled('number')) {
            $query->where('number', $request->number);
        }

        if ($request->trashed === 'true') {
            $query->onlyTrashed();
        }

        if ($request->filled('include')) {
            $allowedIncludes = ['team', 'goals'];
            $includes = explode(',', $request->include);
            $validIncludes = array_intersect($includes, $allowedIncludes);

            if (!empty($validIncludes)) {
                $query->with($validIncludes);
            }
        }

        $players = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Retrieve data success.',
            'data' => $players,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'nullable|exists:teams,id',
            'name' => 'required|string|max:255',
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('players', 'number')->where(fn($q) => $q->where('team_id', $request->team_id)),
            ],
            'position' => 'required|in:GK,DF,MF,FW',
            'height' => 'nullable|integer|min:1',
            'weight' => 'nullable|integer|min:1',
        ]);

        $player = Player::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Player created successfully.',
            'data' => $player,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Retrieve data success.',
            'data' => $player,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Player $player)
    {
        $validated = $request->validate([
            'team_id' => 'nullable|exists:teams,id',
            'name' => 'required|string|max:255',
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('players', 'number')
                    ->where(fn($query) => $query->where('team_id', $request->team_id))
                    ->ignore($player->id),
            ],
            'position' => 'required|in:GK,DF,MF,FW',
            'height' => 'nullable|integer|min:1',
            'weight' => 'nullable|integer|min:1',
        ]);

        $player->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Player updated successfully.',
            'data' => $player,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        $player->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Player deleted successfully.',
        ]);
    }
}
