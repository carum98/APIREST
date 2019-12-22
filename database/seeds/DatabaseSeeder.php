<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Category;
use App\Product;
use App\Transaction;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();

        User::flushEventListeners();
        Category::flushEventListeners();
        Product::flushEventListeners();
        Transaction::flushEventListeners();

        $cantidadUsuarios = 1000;
        $cantidadCategories = 30;
        $cantidadProducts = 1000;
        $cantidadTransactions = 1000;

        factory(User::class, $cantidadUsuarios)->create();

        factory(Category::class, $cantidadCategories)->create();

        factory(Product::class, $cantidadProducts)->create()->each(
            function ($producto) {
                $categorias = Category::all()->random(mt_rand(1, 5))->pluck('id');

                $producto->categories()->attach($categorias);
            });

        factory(Transaction::class, $cantidadTransactions)->create();

    }
}
