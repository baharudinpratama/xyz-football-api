<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Team::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('is_internal')) {
            $query->where('is_internal', filter_var($request->is_internal, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('year_from')) {
            $query->where('year_founded', '>=', $request->year_from);
        }

        if ($request->filled('year_to')) {
            $query->where('year_founded', '<=', $request->year_to);
        }

        if ($request->filled('include')) {
            $allowedIncludes = ['players', 'homeMatches', 'awayMatches'];
            $includes = explode(',', $request->include);
            $validIncludes = array_intersect($includes, $allowedIncludes);

            if (!empty($validIncludes)) {
                $query->with($validIncludes);
            }
        }

        $teams = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Retrieve data success.',
            'data' => $teams,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year_founded' => 'required|integer|min:1900|max:' . date('Y'),
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'is_internal' => 'boolean',
        ]);

        $existing = Team::withTrashed()->where('name', $validated['name'])->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                $existing->update($validated);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Team restored successfully.',
                    'data' => $existing,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Team name already exists.',
                ], 422);
            }
        }

        $team = Team::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Team created successfully.',
            'data' => $team,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load('players');

        return response()->json([
            'status' => 'success',
            'message' => 'Retrieve data success.',
            'data' => $team,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:teams,name,' . $team->id,
            'year_founded' => 'sometimes|integer|min:1900|max:' . date('Y'),
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'is_internal' => 'boolean',
        ]);

        $team->update($validated);
        $team->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Team updated successfully.',
            'data' => $team,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Team deleted successfully.',
        ]);
    }

    public function uploadLogo(Request $request, Team $team)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($team->logo && Storage::disk('public')->exists($team->logo)) {
            Storage::disk('public')->delete($team->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');

        $team->logo = $path;
        $team->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Logo uploaded successfully.',
            'data' => $team,
        ]);
    }
}
