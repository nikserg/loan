<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\LoanController;
use Illuminate\Support\Facades\Route;

// API Routes
Route::prefix('api')->group(function () {
    // Client routes
    Route::apiResource('clients', ClientController::class);

    // Loan routes
    Route::get('loans', [LoanController::class, 'index']);
    Route::get('loans/{id}', [LoanController::class, 'show']);

    // Client-specific loan routes
    Route::get('clients/{clientId}/loans', [LoanController::class, 'clientLoans']);
    Route::post('clients/{clientId}/loans', [LoanController::class, 'apply']);
    Route::get('clients/{clientId}/eligibility', [LoanController::class, 'checkEligibility']);
});
