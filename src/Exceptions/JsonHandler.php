<?php

namespace Handli\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Exception;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;

/**
 * Json handler class
 *
 * Config: config/app.php or config/handli.php
 * 'force_json_response' => true,
 * 'debug' => false,
 * Or add acept application/json header to the curl request
 */
class JsonHandler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array<int, class-string<Throwable>>
	 */
	protected $dontReport = [
		//
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
		$this->reportable(function (Throwable $e) {
			//
		});

		if(
			request()->wantsJson()
			|| config('app.force_json_response') == true
			|| config('handli.force_json_response') == true
		) {

			$this->renderable(function (MethodNotAllowedHttpException $e, $request) {
				return response()->json(['message' => 'Method Not Allowed'], 405);
			});

			$this->renderable(function (InvalidParameterException $e, $request) {
				return response()->json(['message' => 'Invalid Parametr'], 422);
			});

			$this->renderable(function (ResourceNotFoundException $e, $request) {
				return response()->json(['message' => 'Resource Not Found'], 404);
			});

			$this->renderable(function (RouteNotFoundException $e, $request) {
				return response()->json(['message' => 'Route Not Found'], 404);
			});

			$this->renderable(function (MissingMandatoryParametersException $e, $request) {
				return response()->json(['message' => 'Missing Parameters'], 422);
			});

			$this->renderable(function (HttpResponseException $e, $request) {
				return response()->json(['message' => 'Http Response'], 422);
			});

			$this->renderable(function (NotFoundHttpException $e, $request) {
				return response()->json(['message' => 'Http Not Found'], 404);
			});

			$this->renderable(function (PostTooLargeException $e, $request) {
				return response()->json(['message' => 'Post Too Large'], 422);
			});

			$this->renderable(function (ThrottleRequestsException $e, $request) {
				return response()->json(['message' => 'Too Many Requests'], 429);
			});

			$this->renderable(function (JsonResponse $e, $request) {
				$json = json_decode($e->content());
				if(!empty($json->message)) {
					return response()->json(['message' => $json->message], 422);
				} else {
					return response()->json(['message' => $e->content()], 422);
				}
			});

			$this->renderable(function (Exception $e, $request) {
				$code = (int) $e->getCode();

				if($code < 400) {
					$code = 422;
				}

				$res['message'] = $e->getMessage();

				if (config('app.debug')) {
					if (config('handli.debug') == true) {
						$res['code'] = $code;
						$res['ex'] = get_class($e);
						$res['file'] = $e->getFile();
						$res['line'] = $e->getLine();
						$res['trace'] = $e->getTrace();
					}
				}

				return response()->json($res, $code);
			});

			$this->renderable(function (Throwable $e, $request) {
				return response()->json(['message' => 'Unauthorized'], 401);
			});
		}
	}
}
