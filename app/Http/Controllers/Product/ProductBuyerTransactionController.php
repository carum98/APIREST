<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Product;
use App\Transaction;
use App\Transformers\TransactionTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:'.TransactionTransformer::class)->only(['store']);
        $this->middleware('scope:purchase-product')->only('store');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Product $product
     * @param User $buyer
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => 'required|min:1'
        ];

        $this->validate($request, $rules);

        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('El comprador debe ser diferente al vendedor', 409);
        }

        if (!$buyer->esVerificado()) {
            return $this->errorResponse('El comprador debe estar verificado', 409);
        }

        if (!$product->seller->esVerificado()) {
            return $this->errorResponse('El vendedor debe estar verificado', 409);
        }

        if (!$product->estaDisponble()) {
            return $this->errorResponse('El producto para esta transaccion no esta disponible', 409);
        }

        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('El produco no tiene la cantidad disponible requerida para esta transaccion', 409);
        }

        return DB::transaction(function () use ($request, $product, $buyer) {
           $product->quantity -= $request->quantity;
           $product->save();

           $transaction = Transaction::create([
              'quantity' => $request->quantity,
              'buyer_id' => $buyer->id,
               'product_id' => $product->id
           ]);

           return $this->showOne($transaction, 201);
        });
    }

}
