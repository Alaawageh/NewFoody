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
use App\Models\Product;
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
Route::post('/get-token-by-uuid', [AuthController::class, 'getTokenByUUID']);

Route::middleware(['auth:sanctum','SuperAdmin'])->group(function() {
    
    Route::get('/restaurant',[RestaurantController::class,'index']);
    Route::post('/restaurant/add',[RestaurantController::class,'store']);
    Route::patch('/restaurant/{restaurant}',[RestaurantController::class,'update']);
    Route::delete('/restaurant/{restaurant}',[RestaurantController::class,'delete']);

    Route::post('/branch/add',[BranchController::class,'store']);
    Route::patch('/branch/{branch}',[BranchController::class,'update']);
    Route::delete('/branch/{branch}',[BranchController::class,'delete']);

    Route::post('users/add',[UserController::class,'store']);
    Route::post('users/{user}',[UserController::class,'update']);
    Route::delete('user/{user}',[UserController::class,'delete']);


    Route::post('/waiter/add',[UserController::class,'AddWaiter']);
    Route::post('/waiter/edit/{waiter}',[UserController::class,'EditWaiter']);
    Route::delete('/waiter/delete/{waiter}',[UserController::class,'deleteWaiter']);

});

Route::middleware(['auth:sanctum','Admin'])->group(function() {

    Route::get('/restaurant/{restaurant}',[RestaurantController::class,'show']);

    Route::get('/branch/{branch}',[BranchController::class,'show']);
    Route::get('/branch/restaurant/{restaurant}',[BranchController::class,'getBranches']);

    Route::post('/offer/add',[OfferController::class,'store']);
    Route::post('/offer/{offer}',[OfferController::class,'update']);
    Route::delete('/offer/{offer}',[OfferController::class,'delete']);

    Route::get('admin/Show/{category}',[CategoryController::class,'adminShow']);
    Route::get('/admin/category/branch/{branch}',[CategoryController::class,'adminCategory']);
    Route::post('/category/add',[CategoryController::class,'store']);
    Route::post('/category/{category}',[CategoryController::class,'update']);
    Route::delete('/category/{category}',[CategoryController::class,'delete']);
    Route::post('/category/status/{category}',[CategoryController::class,'changeStatus']);

    Route::post('/table/add',[TableController::class,'store']);
    Route::patch('/table/{table}',[TableController::class,'update']);
    Route::delete('/table/{table}',[TableController::class,'delete']);

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
    Route::post('/edit/ingredient/{product}',[ProductController::class,'editIng']);
    Route::post('/edit/extra/{product}',[ProductController::class,'editExtra']);
    Route::post('/delete/extra/{product}/{extraIngredient}',[ProductController::class,'deleteExtra']);
    Route::post('/delete/ingredient/{product}/{ingredient}',[ProductController::class,'deleteIng']);
    Route::put('/product/ingredient/{product_id}/{ingredient_id}',[ProductController::class,'editIsRemove']);

    Route::get('/orders',[OrderController::class,'index']);
    Route::get('/order/{order}',[OrderController::class,'show']);
    Route::get('/order/branch/{branch}',[OrderController::class,'getByBranch']);
    Route::get('/order/table/{table}',[OrderController::class,'getByTable']);
    Route::delete('/order/{order}',[OrderController::class,'delete']);
    Route::get('/orders/feedback',[OrderController::class,'getfeedbacks']);

    Route::get('users/branch/{branch}',[UserController::class,'GetUserByBranch']);
    Route::get('user/{user}',[UserController::class,'show']);

    Route::get('/ratings',[RatingController::class,'index']);

    Route::post('/totalSales/{branch}',[HomeController::class,'TotalSalesByMonth']);
    Route::get('/maxSales/{branch}',[HomeController::class,'maxSales']);
    Route::post('max/{branch}',[HomeController::class,'GetMax']);
    Route::get('/avgSalesByYear/{branch}',[HomeController::class,'avgSalesByYear']);
    Route::post('/mostRequestedProduct/{branch}',[HomeController::class,'mostRequestedProduct']);
    Route::post('/leastRequestedProduct/{branch}',[HomeController::class,'leastRequestedProduct']);
    Route::post('/mostRatedProduct/{branch}',[HomeController::class,'mostRatedProduct']);
    Route::post('/leastRatedProduct/{branch}',[HomeController::class,'leastRatedProduct']);
    Route::post('/orderByDay/{branch}',[HomeController::class,'countOrder']);
    Route::post('/peakTimes/{branch}',[HomeController::class,'peakTimes']);
    Route::post('/statistics/{branch}',[HomeController::class,'statistics']);
    Route::get('/preparationTime/{branch}',[HomeController::class,'readyOrder']);
    Route::get('/timefromDone/{branch}',[HomeController::class,'timefromDone']);
    Route::get('/timeReady/{branch}',[HomeController::class,'timeReady']);
    Route::post('avgRateOrder/{branch}',[HomeController::class,'avgRatingOrder']);
    Route::get('/feedbacks/{branch}',[HomeController::class,'getfeedbacks']);
    Route::post('/product/avgRating/{branch}',[HomeController::class,'avgRatingProduct']);
    Route::post('/waiter/countTables/{branch}',[HomeController::class,'countTables']);
});


Route::middleware(['auth:sanctum','Kitchen'])->group(function() {
    Route::delete('/kitchen/order/{order}',[KitchenController::class,'delete']);
    Route::get('orders/kitchen/{branch}',[KitchenController::class,'getOrders']);
    Route::post('order/ChangeToPrepare/{order}',[KitchenController::class,'ChangeToPreparing']);
    Route::post('order/ChangeToDone/{order}',[KitchenController::class,'ChangeToDone']);
    Route::get('/getToDone/{branch}',[KitchenController::class,'getToDone']);
});

Route::middleware(['auth:sanctum','Waiter'])->group(function() {
    Route::get('/waiter/branch/{branch}',[UserController::class,'getwaiterByBranch']);
    Route::get('orders/waiter/{branch}',[WaiterController::class,'getOrder']);
    Route::post('orders/done/{order}',[WaiterController::class,'done']);
});

Route::middleware(['auth:sanctum','Casher'])->group(function(){
    Route::get('orders/Casher/{branch}',[CasherController::class,'getOrders']);
    Route::post('order/ChangeToPaid/{bill}',[CasherController::class,'ChangeToPaid']);
});
Route::post('/login',[AuthController::class,'login']);

Route::get('/offer/{offer}',[OfferController::class,'show']);
Route::get('/offer/branch/{branch}',[OfferController::class,'getOffers']);

Route::get('/category/{category}',[CategoryController::class,'show']);
Route::get('/category/branch/{branch}',[CategoryController::class,'getCategories']);

Route::get('/product/{product}',[ProductController::class,'show']);
Route::get('/products/category/{category}',[ProductController::class,'getProducts']);
Route::get('/products/branch/{branch}/{category}',[ProductController::class,'getproductByBranch']);
Route::get('/products/branch/{branch}',[ProductController::class,'getAllbyBranch']);

Route::get('/extraIng',[ExtraIngController::class,'index']);
Route::get('/extraIng/{ExtraIngredient}',[ExtraIngController::class,'show']);
Route::get('/extraIng/product/{product}',[ExtraIngController::class,'getByProduct']);
Route::get('/extraIng/branch/{branch}',[ExtraIngController::class,'getByBranch']);
Route::get('/extraIng/ingredient/{ingredient}',[ExtraIngController::class,'getByIngredient']);

Route::post('/order/add',[OrderController::class,'store']);
Route::post('/order/getOrderForEdit',[OrderController::class,'getOrderForEdit']);
Route::post('/order/{order}',[OrderController::class,'update']);

Route::get('/cart/showToRate/{branch}/{table}',[OrderController::class,'getOrderforRate']);
Route::post('/rating/products/add',[RatingController::class,'add']);
Route::post('/rating/service/add/{bill}',[OrderController::class,'AddRate']);

Route::get('/table',[TableController::class,'index']);
Route::get('/table/branch/{branch}',[TableController::class,'getTables']);

Route::get('taxRate/branch/{branch}',[BranchController::class,'getTax']);

Route::get('/removed/ingredient',[ProductController::class,'getRemoveIng']);
Route::get('remove/ingredient/product/{product}',[ProductController::class,'getRemoveByProduct']);

Route::post('waiter/call',[WaiterController::class,'callWaiter']);

Route::get('/ingredient/product/{product}',[ProductController::class,'getIngredients']);

