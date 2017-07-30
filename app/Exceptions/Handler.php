<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use App\Traits\ApiResponser as ApiResponser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
        
        if($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }
        elseif ($exception instanceof AuthorizationException) {
            return $this->errorResponse('No posee permisos para ejecutar esta acción',403);
        }
        elseif ($exception instanceof NotFoundHttpException){
            return $this->errorResponse('No se encontro la url solicitada',404);
        }
        elseif ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('Este metodo no es valido',405);
        }
        elseif ($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }
        elseif ($exception instanceof QueryException){
            // dd($exception);
            $codigo = $exception->errorInfo[1];
            if($codigo ==1451){
                return $this->errorResponse('No se puede eliminar de forma permanente el recurso porque esta relacionada con algún otro.',409);
            }
            
        }
        // if (config('app.debug')) {
        //     # code...
        //     return $this->errorResponse('falla inesperda, intente luego',500);
        // }
        else{
            return parent::render($request, $exception);
        }    
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

}
