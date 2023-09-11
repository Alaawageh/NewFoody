<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AddProductRequest;
use App\Http\Requests\Product\EditProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $products = ProductResource::collection(Product::orderByRaw('position IS NULL ASC, position ASC')->get());
        return $this->apiResponse($products,'success',200); 
    }

    public function getProducts(Category $category)
    {
        $products = $category->product()->orderByRaw('position IS NULL ASC, position ASC')->get();
        return $this->apiResponse(ProductResource::collection($products),'success',200);
    }

    public function show(Product $product)
    {
        return $this->apiResponse(ProductResource::make($product),'success',200);
    }

    public function store(AddProductRequest $request , Product $product)
    {
        $request->validated($request->all());

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

        return $this->apiResponse(new ProductResource($product),'Data Successfully Saved',201);
    }

    public function update(EditProductRequest $request , Product $product)
    {
        $request->validated($request->all());

        if($request->hasFile('image')) {
            File::delete(public_path($product->image));

            $product->update(array_merge($request->except('position'),
            ['estimated_time' => Carbon::createFromTimestamp($request->estimated_time)->format('i:s')]));
        }

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
}
