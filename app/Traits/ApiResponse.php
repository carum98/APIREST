<?php

namespace App\Traits;


//use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponse
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    public function errorResponse($message, $code)
    {
        return response()->json(['error'=>$message, 'code'=>$code], $code);
    }

    public function showAll(Collection $collection, $code = 200)
    {
        if ($collection->isEmpty())
        {
            return $this->successResponse(['data' => $collection], $code);
        }
        $transformer = $collection->first()->transformer;
        $collection = $this->transforData($collection, $transformer);
        return $this->successResponse($collection, $code);
    }

    public function showOne(Model $instance, $code = 200)
    {
        $transfomer = $instance->transformer;
        $instance = $this->transforData($instance, $transfomer);
        return $this->successResponse($instance, $code);
    }

    public function showMessage($message, $code = 200) {
        return $this->successResponse(['data' => $message], $code);
    }

    protected function transforData($data, $tranformer)
    {
        $traformation = fractal($data, new $tranformer);
        return $traformation->toArray();
    }
}
