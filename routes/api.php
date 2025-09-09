<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokenController;


Route::post('/tokens/validate', [TokenController::class, 'validateTokenFormat'])
    ->name('tokens.validate');