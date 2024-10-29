<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Category;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CategoryController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            dd(Auth::user());
            $categories = Category::all();
            return $this->sendSuccess(
                data: $categories,
                message: 'Successfully Fetched Category ',
                status: true,
                statusCode: 200

            );
        } catch (Exception $ex) {

            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203

            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:categories',
            ]);
            $category = new Category();
            $category->name = $request->name;
            $category->save();

            return $this->sendSuccess(
                data: $category,
                message: 'Category Successfully Saved',
                status: true,
                statusCode: 201

            );
        } catch (Exception $ex) {

            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203

            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
