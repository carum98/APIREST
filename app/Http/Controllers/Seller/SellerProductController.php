<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Product;
use App\Seller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;


class SellerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;
        return $this->showAll($products);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Seller $seller
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image'
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::PRODUCTO_NO_DISPONIBLE;
        $data['image'] = $request->image->store('');

        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return $this->showOne($product, 201);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Seller $seller
     * @param Product $product
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
          'quantity' => 'integer|min:1',
          'status' => 'in:'.Product::PRODUCTO_DISPONIBLE.','. Product::PRODUCTO_NO_DISPONIBLE,
          'image' => 'image'
        ];

        $this->validate($request, $rules);

        $this->verificarVendedor($seller, $product);

        $product->fill($request->only([
            'name',
            'description',
            'quantity'
        ]));

        if ($request->has('status')) {
            $product->status = $request->status;

            if ($product->estaDisponble() && $product->categories()->count() == 0){
                return $this->errorResponse('Un producto activo debe tener al meno una categoria', 409);
            }
        }

        if ($request->hasFile('image')) {
            Storage::delete($product->image);
            $product->image = $request->image->store('');
        }

        if ($product->isClean()) {
            return $this->errorResponse('Se debe especificar al menos un valor diferente', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Seller $seller
     * @param Product $product
     * @return void
     * @throws \Exception
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->verificarVendedor($seller, $product);
        Storage::delete($product->image);
        $product->delete();
        return $this->showOne($product);
    }

    public function verificarVendedor(Seller $seller, Product $product){
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422, 'El vendedor especificado no es el vendedor real del producto');
        }
    }
}
