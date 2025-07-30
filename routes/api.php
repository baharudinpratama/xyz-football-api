<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\MatchResultController;
use App\Http\Controllers\Api\MatchScheduleController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('teams', TeamController::class);
    Route::post('teams/{team}/logo', [TeamController::class, 'uploadLogo']);

    Route::apiResource('players', PlayerController::class);

    Route::apiResource('matches', MatchScheduleController::class);

    Route::prefix('matches/{match}')->group(function () {
        Route::get('report', [MatchScheduleController::class, 'showMatchReport']);

        Route::post('result', [MatchResultController::class, 'store']);
        Route::get('result', [MatchResultController::class, 'show']);
        Route::put('result', [MatchResultController::class, 'update']);
        Route::delete('result', [MatchResultController::class, 'destroy']);

        Route::get('goals', [GoalController::class, 'indexByMatch']);
        Route::post('goals', [GoalController::class, 'store']);
    });

    Route::apiResource('goals', GoalController::class)->except('show', 'store', 'show');
});
