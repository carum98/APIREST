<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    protected function allowedAdminAction() {
        if (Gate::denies('admin-actions')) {
            throw new AuthorizationException('Esta accion no es permitida');
        }
    }
}
