<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilesController;


Route::post('/files', [FilesController::class, 'store']);
