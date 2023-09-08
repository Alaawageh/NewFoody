<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdmin\BranchController;
use App\Http\Controllers\SuperAdmin\RestaurantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/logout',[AuthController::class,'logout']);

    Route::post('/restaurant/add',[RestaurantController::class,'store']);
    Route::patch('/restaurant/{restaurant}',[RestaurantController::class,'update']);
    Route::get('/restaurant/{restaurant}',[RestaurantController::class,'show']);
    Route::delete('/restaurant/{restaurant}',[RestaurantController::class,'delete']);

    Route::get('/branch',[BranchController::class,'index']);
    Route::get('/branch/{branch}',[BranchController::class,'show']);
    Route::get('/branch/restaurant/{restaurant}',[BranchController::class,'getBranches']);
    Route::post('/branch/add',[BranchController::class,'store']);
    Route::patch('/branch/{branch}',[BranchController::class,'update']);
    Route::delete('/branch/{branch}',[BranchController::class,'delete']);

    Route::get('/offer',[OfferController::class,'index']);
    Route::get('/offer/{offer}',[OfferController::class,'show']);
    Route::get('/offer/branch/{branch}',[OfferController::class,'getOffers']);
    Route::post('/offer/add',[OfferController::class,'store']);
    Route::post('/offer/{offer}',[OfferController::class,'update']);
    Route::delete('/offer/{offer}',[OfferController::class,'delete']);

    Route::get('/category',[CategoryController::class,'index']);
    Route::get('/category/{category}',[CategoryController::class,'show']);
    Route::get('/category/branch/{branch}',[CategoryController::class,'getCategories']);
    Route::post('/category/add',[CategoryController::class,'store']);
    Route::post('/category/{category}',[CategoryController::class,'update']);
    Route::delete('/category/{category}',[CategoryController::class,'delete']);

    Route::get('/table',[TableController::class,'index']);
    Route::get('/table/branch/{branch}',[TableController::class,'getTables']);
    Route::post('/table/add',[TableController::class,'store']);
    Route::patch('/table/{table}',[TableController::class,'update']);
    Route::delete('/table/{table}',[TableController::class,'delete']);
});
Route::post('/login',[AuthController::class,'login']);

