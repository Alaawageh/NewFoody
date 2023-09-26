<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AddProductRequest;
use App\Http\Requests\Product\EditProductRequest;
use App\Http\Resources\ProductIngredientResource;
use App\Http\Resources\ProductResource;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ExtraIngredient;
use App\Models\Product;
use App\Models\ProductExtraIngredient;
use App\Models\ProductIngredient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    use ApiResponseTrait;

    // public function index()
    // {
    //     $products = ProductResource::collection(Product::with('extraIngredients')->where('status',1)->orderByRaw('position IS NULL ASC, position ASC')->get());
    //     return $this->apiResponse($products,'success',200); 
    // }
    // public function GetAll()
    // {
    //     $products = ProductResource::collection(Product::with('extraIngredients')->orderByRaw('position IS NULL ASC, position ASC')->get());
    //     return $this->apiResponse($products,'success',200); 
    // }

    public function show(Product $product)
    {
        if($product->status == 1) {
            return $this->apiResponse(ProductResource::make($product),'success',200);
        }else{
            return $this->apiResponse(null,'Not Found',200);
        }
    }

    public function getProducts(Category $category)
    {
        if($category->status == 1) {
            $products = $category->product()->where('status',1)->orderByRaw('position IS NULL ASC, position ASC')->get();
            return $this->apiResponse(ProductResource::collection($products),'success',200);
        }else{
            return $this->apiResponse(null,'Not Found',404);

        }

    }
    public function getproductByBranch(Branch $branch)
    {
        $products = $branch->product()->where('status',1)->orderByRaw('position IS NULL ASC, position ASC')->get();
        return $this->apiResponse(ProductResource::collection($products),'success',200);
    }

    public function getByCategory(Category $category)
    {
        $products = $category->product()->orderByRaw('position IS NULL ASC, position ASC')->get();
        return $this->apiResponse(ProductResource::collection($products),'success',200);
    }



    public function store(AddProductRequest $request , Product $product)
    {
        $request->validated($request->all());
        DB::beginTransaction();
        try{

            $product = Product::create($request->except('position'));

            if($request->position) {
                $products = Product::where('category_id',$request->category_id)->orderBy('position')->get();
                if ($products->isNotEmpty()) {
                    foreach ($products as $pro) {
                        if($pro->position >= $request->position && $pro->position != null ){
                            $pro->position++;
                            $pro->save();
                        } 
                    }
                    $product->position = $request->position;
                }

            }
            $product->save();
            $product->ReOrder($request);

            if (is_array($request->ingredients)) {
                foreach ($request->ingredients as $ingredient) {
                    $product->ingredients()->attach($ingredient['id'], ['quantity' => $ingredient['quantity'],'is_remove' => $ingredient['is_remove']]);
                }
            }
            if (is_array($request->extra_ingredients)) {
                foreach ($request->extra_ingredients as $extraIngredient) {
                    $extra = ExtraIngredient::find($extraIngredient['id']);
                    if($extra) {
                        ProductExtraIngredient::create([
                            'product_id' => $product['id'],
                            'extra_ingredient_id' => $extraIngredient['id'],
                            'quantity' => $extraIngredient['quantity'],
                            'price_per_piece' => ($extra->price_per_kilo * $extraIngredient['quantity'])/1000,
                        ]);
                    }

                    
                    // $product->extraIngredients()->attach($extraIngredient['id'], ['quantity' => $extraIngredient['quantity']]);
                }
            }
            DB::commit();
            return $this->apiResponse(new ProductResource($product),'Data Successfully Saved',201);
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function update(EditProductRequest $request , Product $product)
    {
        $request->validated($request->all());

        if($request->hasFile('image')) {
            File::delete(public_path($product->image));
        }
        $product->update($request->except('position'));

        if($request->position) {

            $products = Product::where('category_id',$request->category_id)->orderBy('position')->get();
            foreach ($products as $pro) {
                if($pro->position >= $request->position && $pro->position != null ){
                    $pro->position++;
                    $pro->save();
                } 
            }
            $product->position = $request->position;
        }
        $product->save();
        $product->ReOrder($request);
        $product->ingredients()->detach();
        if (is_array($request->ingredients)) {
            foreach ($request->ingredients as $ingredient) {
                $product->ingredients()->attach($ingredient['id'], ['quantity' => $ingredient['quantity'],'is_remove' => $ingredient['is_remove']]);
            }
        }
        $product->extraIngredients()->detach();
        if (is_array($request->extra_ingredients)) {
            foreach ($request->extra_ingredients as $extraIngredient) {
                $extra = ExtraIngredient::find($extraIngredient['id']);
                if($extra) {
                    ProductExtraIngredient::create([
                        'product_id' => $product['id'],
                        'extra_ingredient_id' => $extraIngredient['id'],
                        'quantity' => $extraIngredient['quantity'],
                        'price_per_piece' => ($extra->price_per_kilo * $extraIngredient['quantity'])/1000,
                    ]);
                }

                // $product->extraIngredients()->attach($extraIngredient['id'], ['quantity' => $extraIngredient['quantity']]);
            }
        }
        $product->save();
        return $this->apiResponse(ProductResource::make($product),'Data Successfully Saved',200);
    }

    public function delete(Product $product)
    {
        File::delete(public_path($product->image));

        $product->delete();

        return $this->apiResponse(null,'Data successfully Deleted',200);
    }

    public function changeStatus(Product $product)
    {
        $product->status == 1 ? $product->status = 0 : $product->status = 1;

        $product->save();

        return $this->apiResponse($product->status,'Status change successfully.',200);
    }

    public function getByBranch(Branch $branch)
    {
        $products = $branch->product()->orderByRaw('position IS NULL ASC, position ASC')->get();
        return $this->apiResponse(ProductResource::collection($products),'success',200);
    }

    public function getRemoveIng()
    {
        $removed = ProductIngredient::with('product.branch','product.category')->where('is_remove', 1)->get();
        return $this->apiResponse(ProductIngredientResource::collection($removed),'success',200);
    }
}
