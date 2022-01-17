<?php

namespace Handli\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

// use Exception;
// use Symfony\Component\Routing\Exception\InvalidParameterException;
// use Symfony\Component\Routing\Exception\ResourceNotFoundException;
// use Symfony\Component\Routing\Exception\RouteNotFoundException;
// use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
// use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
// use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
// use Illuminate\Http\Exceptions\HttpResponseException;
// use Illuminate\Http\Exceptions\PostTooLargeException;
// use Illuminate\Http\Exceptions\ThrottleRequestsException;
// use Illuminate\Http\JsonResponse;

class ApiHandler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array<int, class-string<Throwable>>
	 */
	protected $dontReport = [
		// InvalidOrderException::class,
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array<int, string>
	 */
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	/**
	 * Register the exception handling callbacks for the application.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->renderable(function (Throwable $e, $request) {
			// Json response
			if (
				$request->is('web/*') ||
				$request->is('api/*') ||
				$request->wantsJson()
			) {
				$msg = empty($e->getMessage()) ? 'Not Found' : $e->getMessage();
				$code = empty($e->getCode()) ? 404 : $e->getCode();

				return response()->json([
					'message' => $msg,
					'code' => $code,
					'ex' => [
						'name' => $this->getClassName(get_class($e)),
						'namespace' => get_class($e),
					]
				], $code);
			}
		});

		$this->reportable(function (Throwable $e) {
			// Stop logging to lavavel logs
			// return false;
		});
	}

	/**
	 * Get exception class name without namespace.
	 *
	 * @return string
	 */
	static function getClassName($e) {
		$path = explode('\\', $e);
		return array_pop($path);
	}
}
