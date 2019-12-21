<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        if ($exception instanceof ModelNotFoundException){
            $model = class_basename($exception->getModel());
            return $this->errorResponse("No existe ninguna instacia de {$model} con el id especificado", 404);
        }
        if ($exception instanceof AuthenticationException){
            return $this->unauthenticated($request, $exception);
        }
        if ($exception instanceof AuthorizationException){
            return $this->errorResponse("No posee los permisos necesario", 403);
        }
        if ($exception instanceof NotFoundHttpException){
            return $this->errorResponse("No se encontro la URL especificada", 404);
        }
        if ($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse("El metodo en la peticion no es valido", 405);
        }
        if ($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }
        if ($exception instanceof QueryException){
            $code = $exception->errorInfo[1];
            if ($code == 1451) {
                return $this->errorResponse('No se puede eliminar porque esta realcionado con otro', 409);
            }
        }

        if (config('app.debug')){
            return parent::render($request, $exception);
        }
        return $this->errorResponse('Falla inesperada', 500);
    }


    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $error = $e->validator->errors()->getMessages();
        return $this->errorResponse($error, 422);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse('No autenticado', 401);
    }
}
