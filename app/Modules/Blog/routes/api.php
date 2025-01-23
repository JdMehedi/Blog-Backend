<?php

use App\Http\Middleware\HandleCors;
use App\Modules\Blog\Http\Controllers\BlogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::post('store/blog', [BlogController::class,'storeBlog'])->middleware(HandleCors::class);
Route::get('index/blog', [BlogController::class,'index']);
Route::get('view/blog', [BlogController::class,'view']);
Route::put('update/blog', [BlogController::class,'update']);
