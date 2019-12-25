<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getTokens()
    {
        return view('personal_tokens');
    }

    public function getClients()
    {
        return view('personal_clients');
    }

    public function getAuthorizeClients()
    {
        return view('personal_authorize_clients');
    }
}
