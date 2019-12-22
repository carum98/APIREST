<?php

namespace App\Providers;

use App\Mail\UserCreated;
use App\Mail\UserMailChange;
use App\Product;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        User::created(function (User $user) {
            retry(5, function () use ($user) {
               Mail::to($user)->send(new UserCreated($user));
            }, 100);
        });

        User::updated(function (User $user) {
            if ($user->isDirty('email')) {
                retry(5, function () use ($user) {
                   Mail::to($user)->send(new UserMailChange($user));
                }, 100);
            }
        });

        Product::updated(function (Product $product) {
            if ($product->quantity == 0 && $product->estaDisponble()) {
                $product->status = Product::PRODUCTO_NO_DISPONIBLE;
                $product->save();
            }
        });
    }
}
