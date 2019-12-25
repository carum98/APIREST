<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Product $product
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Product $product)
    {
        $this->allowedAdminAction();

        $transaction = $product->transactions;
        return $this->showAll($transaction);
    }
}
