<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddTableRequest;
use App\Http\Requests\EditTableRequest;
use App\Http\Resources\TableResource;
use App\Models\Branch;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $tables = TableResource::collection(Table::get());
        return $this->apiResponse($tables,'success',200);
    }

    public function getTables(Branch $branch)
    {
        $tables = $branch->tables()->get();
        return $this->apiResponse(TableResource::collection($tables),'success',200);
    }

    public function store(AddTableRequest $request)
    {
        $request->validated($request->all());
        
        $table = Table::create($request->all());

        return $this->apiResponse(new TableResource($table),'Data successfully Saved',201);
    }

    public function update(EditTableRequest $request, Table $table)
    {
        $request->validated($request->all());
        
        $table->update($request->all());

        return $this->apiResponse(TableResource::make($table),'Data successfully Saved',201);
    }

    public function delete(Table $table)
    {
        $table->delete();
        return $this->apiResponse(null,'Data successfully deleted',200);

    }
}
