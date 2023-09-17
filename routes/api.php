<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ExtraIngController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Casher\CasherController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SuperAdmin\BranchController;
use App\Http\Controllers\SuperAdmin\RestaurantController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Waiter\WaiterController;
use App\Models\Rating;
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

Route::middleware(['auth:sanctum','SuperAdmin'])->group(function() {
    
    Route::post('/restaurant/add',[RestaurantController::class,'store']);
    Route::patch('/restaurant/{restaurant}',[RestaurantController::class,'update']);
    Route::get('/restaurant/{restaurant}',[RestaurantController::class,'show']);
    Route::delete('/restaurant/{restaurant}',[RestaurantController::class,'delete']);
    // Route::get('/branch',[BranchController::class,'index']);
    


});
Route::middleware(['auth:sanctum','Admin'])->group(function() {

    Route::get('/branch/{branch}',[BranchController::class,'show']);
    Route::post('/branch/add',[BranchController::class,'store']);
    Route::patch('/branch/{branch}',[BranchController::class,'update']);
    Route::delete('/branch/{branch}',[BranchController::class,'delete']);
    Route::get('/branch/restaurant/{restaurant}',[BranchController::class,'getBranches']);


    Route::post('/offer/add',[OfferController::class,'store']);
    Route::post('/offer/{offer}',[OfferController::class,'update']);
    Route::delete('/offer/{offer}',[OfferController::class,'delete']);

    Route::get('admin/category',[CategoryController::class,'adminAll']);
    Route::get('admin/Show/{category}',[CategoryController::class,'adminShow']);
    Route::get('/admin/category/branch/{branch}',[CategoryController::class,'adminCategory']);
    Route::post('/category/add',[CategoryController::class,'store']);
    Route::post('/category/{category}',[CategoryController::class,'update']);
    Route::delete('/category/{category}',[CategoryController::class,'delete']);
    Route::post('/category/status/{category}',[CategoryController::class,'changeStatus']);

    Route::post('/table/add',[TableController::class,'store']);
    Route::patch('/table/{table}',[TableController::class,'update']);
    Route::delete('/table/{table}',[TableController::class,'delete']);

    Route::get('/ingredients',[IngredientController::class,'index']);
    Route::get('/ingredient/{ingredient}',[IngredientController::class,'show']);
    Route::get('/ingredient/branch/{branch}',[IngredientController::class,'IngByBranch']);
    Route::post('/ingredient/add',[IngredientController::class,'store']);
    Route::post('/ingredient/{ingredient}',[IngredientController::class,'editQty']);
    Route::patch('/ingredient/{ingredient}',[IngredientController::class,'update']);
    Route::delete('/ingredient/{ingredient}',[IngredientController::class,'delete']);

    Route::post('/extraIng/add',[ExtraIngController::class,'store']);
    Route::patch('/extraIng/{ExtraIngredient}',[ExtraIngController::class,'update']);
    Route::delete('/extraIng/{ExtraIngredient}',[ExtraIngController::class,'delete']);

    Route::get('/admin/product/{category}',[ProductController::class,'getByCategory']);
    Route::post('/product/add',[ProductController::class,'store']);
    Route::post('/product/{product}',[ProductController::class,'update']);
    Route::delete('/product/{product}',[ProductController::class,'delete']);
    Route::post('/product/status/{product}',[ProductController::class,'changeStatus']);
    Route::get('/admin/products/branch/{branch}',[ProductController::class,'getByBranch']);

    Route::get('/orders',[OrderController::class,'index']);
    Route::get('/order/{order}',[OrderController::class,'show']);
    Route::get('/order/branch/{branch}',[OrderController::class,'getByBranch']);
    Route::get('/order/table/{table}',[OrderController::class,'getByTable']);
    Route::delete('/order/{order}',[OrderController::class,'delete']);
    Route::get('/orders/feedback',[OrderController::class,'getfeedbacks']);

    Route::post('users/add',[UserController::class,'store']);
    Route::post('users/{user}',[UserController::class,'update']);
    Route::delete('user/{user}',[UserController::class,'delete']);
    Route::get('users/branch/{branch}',[UserController::class,'GetUserByBranch']);
    Route::get('user/{user}',[UserController::class,'show']);

    Route::get('/ratings',[RatingController::class,'index']);

    Route::post('/totalSales/',[HomeController::class,'TotalSalesByMonth']);
    Route::post('/maxSales/',[HomeController::class,'maxSales']);
    Route::post('/avgSalesByYear/',[HomeController::class,'avgSalesByYear']);
    Route::post('/mostRequestedProduct',[HomeController::class,'mostRequestedProduct']);
    Route::post('/leastRequestedProduct',[HomeController::class,'leastRequestedProduct']);
    Route::post('/mostRatedProduct',[HomeController::class,'mostRatedProduct']);
    Route::post('/leastRatedProduct',[HomeController::class,'leastRatedProduct']);
    Route::get('/product/avgRating/{product}',[HomeController::class,'avgRatingProduct']);
    Route::post('/orderByDay',[HomeController::class,'countOrder']);
    Route::post('/peakTimes',[HomeController::class,'peakTimes']);
    Route::post('/statistics',[HomeController::class,'statistics']);
    Route::get('/preparationTime',[HomeController::class,'readyOrder']);
    Route::get('/timefromDone',[HomeController::class,'timefromDone']);
    Route::get('/timeReady',[HomeController::class,'timeReady']);
    Route::post('avgRateOrder',[HomeController::class,'avgRatingOrder']);
    // Route::get('/waiter/countTables',[HomeController::class,'countTables']);
});


Route::middleware(['auth:sanctum','Kitchen'])->group(function() {
    Route::delete('/kitchen/order/{order}',[KitchenController::class,'delete']);
    Route::get('orders/kitchen',[KitchenController::class,'getOrders']);
    Route::post('order/ChangeToPrepare/{order}',[KitchenController::class,'ChangeToPreparing']);
    Route::post('order/ChangeToDone/{order}',[KitchenController::class,'ChangeToDone']);
    Route::get('/getToDone',[KitchenController::class,'getToDone']);
});

Route::middleware(['auth:sanctum','Waiter'])->group(function() {

    Route::get('orders/waiter',[WaiterController::class,'getOrder']);
    Route::post('orders/done/{order}',[WaiterController::class,'done']);
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

Route::get('/product/{product}',[ProductController::class,'show']);
Route::get('/products/category/{category}',[ProductController::class,'getProducts']);
Route::get('/products/branch/{branch}',[ProductController::class,'getproductByBranch']);

Route::get('/extraIng',[ExtraIngController::class,'index']);
Route::get('/extraIng/{ExtraIngredient}',[ExtraIngController::class,'show']);
Route::get('/extraIng/product/{product}',[ExtraIngController::class,'getByProduct']);
Route::get('/extraIng/branch/{branch}',[ExtraIngController::class,'getByBranch']);

Route::post('/order/add',[OrderController::class,'store']);
Route::post('/order/getOrderForEdit',[OrderController::class,'getOrderForEdit']);
Route::post('/order/{order}',[OrderController::class,'update']);

Route::get('/cart/showToRate/{table}',[OrderController::class,'getOrderforRate']);
Route::post('/rating/products/add',[RatingController::class,'add']);
Route::post('/rating/service/add/{order}',[OrderController::class,'storeRate']);

Route::get('/table',[TableController::class,'index']);
Route::get('/table/branch/{branch}',[TableController::class,'getTables']);

