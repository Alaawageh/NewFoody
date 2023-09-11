<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ExtraIngredientController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RepoController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Casher\CasherController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SuperAdmin\BranchController;
use App\Http\Controllers\SuperAdmin\RestaurantController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Waiter\WaiterController;
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

Route::post('/logout',[AuthController::class,'logout'])->middleware(['auth:sanctum']);

Route::middleware(['auth:sanctum','Admin'])->group(function() {
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

    Route::post('/offer/add',[OfferController::class,'store']);
    Route::post('/offer/{offer}',[OfferController::class,'update']);
    Route::delete('/offer/{offer}',[OfferController::class,'delete']);

    Route::post('/category/add',[CategoryController::class,'store']);
    Route::post('/category/{category}',[CategoryController::class,'update']);
    Route::delete('/category/{category}',[CategoryController::class,'delete']);

    Route::get('/table',[TableController::class,'index']);
    Route::get('/table/branch/{branch}',[TableController::class,'getTables']);
    Route::post('/table/add',[TableController::class,'store']);
    Route::patch('/table/{table}',[TableController::class,'update']);
    Route::delete('/table/{table}',[TableController::class,'delete']);

    Route::get('/repository',[RepoController::class,'index']);
    Route::get('/repository/{repo}',[RepoController::class,'show']);
    Route::get('/repository/branch/{branch}',[RepoController::class,'getByBranch']);
    Route::post('/repository/add',[RepoController::class,'store']);
    Route::patch('/repository/{repo}',[RepoController::class,'update']);
    Route::delete('/repository/{repo}',[RepoController::class,'delete']);

    Route::get('/extraIngredient/repo/{repo}',[ExtraIngredientController::class,'getExtra']);
    Route::post('/extraIngredient/add',[ExtraIngredientController::class,'store']);
    Route::patch('/extraIngredient/{extraIngredient}',[ExtraIngredientController::class,'update']);
    Route::delete('/extraIngredient/{extraIngredient}',[ExtraIngredientController::class,'delete']);

    Route::post('/product/add',[ProductController::class,'store']);
    Route::post('/product/{product}',[ProductController::class,'update']);
    Route::delete('/product/{product}',[ProductController::class,'delete']);
    Route::post('/product/status/{product}',[ProductController::class,'changeStatus']);

    Route::get('/orders',[OrderController::class,'index']);
    Route::get('/order/{order}',[OrderController::class,'show']);
    Route::get('/order/branch/{branch}',[OrderController::class,'getByBranch']);
    Route::get('/order/table/{table}',[OrderController::class,'getByTable']);
    Route::delete('/order/{order}',[OrderController::class,'delete']);

    Route::post('users/add',[UserController::class,'store']);

});

Route::middleware(['auth:sanctum','Kitchen'])->group(function(){
    Route::get('orders/kitchen',[KitchenController::class,'getOrders']);
    Route::post('order/ChangeToPrepare/{order}',[KitchenController::class,'ChangeToPreparing']);
    Route::post('order/ChangeToDone/{order}',[KitchenController::class,'ChangeToDone']);
});

Route::middleware(['auth:sanctum','Waiter'])->group(function(){
    Route::post('orders/Waiter',[WaiterController::class,'done']);
});

Route::middleware(['auth:sanctum','Casher'])->group(function(){
    Route::get('orders/Casher',[CasherController::class,'getOrders']);
    Route::post('order/ChangeToPaid/{order}',[CasherController::class,'ChangeToPaid']);
});
Route::post('/login',[AuthController::class,'login']);

Route::get('/offer',[OfferController::class,'index']);
Route::get('/offer/{offer}',[OfferController::class,'show']);
Route::get('/offer/branch/{branch}',[OfferController::class,'getOffers']);

Route::get('/category',[CategoryController::class,'index']);
Route::get('/category/{category}',[CategoryController::class,'show']);
Route::get('/category/branch/{branch}',[CategoryController::class,'getCategories']);

Route::get('/extraIngredient',[ExtraIngredientController::class,'index']);
Route::get('/extraIngredient/{extraIngredient}',[ExtraIngredientController::class,'show']);

Route::get('/products',[ProductController::class,'index']);
Route::get('/product/{product}',[ProductController::class,'show']);
Route::get('/products/category/{category}',[ProductController::class,'getProducts']);

Route::post('/order/add',[OrderController::class,'store']);
Route::post('/order/getOrderForEdit',[OrderController::class,'getOrderForEdit']);
Route::post('/order/{order}',[OrderController::class,'update']);

