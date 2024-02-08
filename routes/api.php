<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("/products/find-by-name/{name}", [ProductController::class, "findByName"])->name("products.find_by_name");
Route::resource("products", ProductController::class)->only("store", "index", "show", "update", "destroy");