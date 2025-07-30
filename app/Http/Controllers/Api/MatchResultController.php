<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchSchedule;
use Illuminate\Http\Request;

class MatchResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MatchSchedule $match)
    {
        $validated = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        if ($match->result) {
            return response()->json([
                'status' => 'error',
                'message' => 'Result for match already exists',
            ], 422);
        }

        $result = $match->result()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Match result created successfully.',
            'data' => $result,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MatchSchedule $match)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Retrieve data success.',
            'data' => $match->result,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MatchSchedule $match)
    {
        $validated = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        $result = $match->result;

        if (!$result) {
            return response()->json([
                'status' => 'error',
                'message' => 'Result not found',
            ], 404);
        }

        $result->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Match result updated successfully.',
            'data' => $result,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MatchSchedule $match)
    {
        $result = $match->result;

        if (!$result) {
            return response()->json([
                'status' => 'error',
                'message' => 'Match result not found',
            ], 404);
        }

        $result->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Match result deleted successfully.',
        ]);
    }
}
