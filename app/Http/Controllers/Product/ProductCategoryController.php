<?php

namespace App\Http\Controllers\Product;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Product $product
     * @return void
     */
    public function index(Product $product)
    {
        $categories = $product->categories;
        return $this->showAll($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     * @param Category $category
     * @return void
     */
    public function update(Request $request, Product $product, Category $category)
    {
        //sync, attach, syncWithoutDetaching
        $product->categories()->syncWithoutDetaching([$category->id]);
        return $this->showAll($product->categories);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Product $product
     * @param Category $category
     * @return void
     */
    public function destroy(Product $product, Category $category)
    {
        if (!$product->categories()->find($category->id)){
            return $this->errorResponse('La categoria especificada no es una categoria de este producto', 404);
        }
        $product->categories()->detach([$category->id]);
        return $this->showAll($product->categories);
    }
}