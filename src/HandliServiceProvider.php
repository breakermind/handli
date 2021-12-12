<?php

namespace Handli;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Handli\Exceptions\JsonHandler;

class HandliServiceProvider extends ServiceProvider
{
	public function register() {
		$this->mergeConfigFrom(__DIR__.'/../config/config.php', 'handli');

		if(config('handli.force_json_response') == true || config('app.force_json_response') == true) {
			$this->app->singleton(ExceptionHandler::class, JsonHandler::class);
		}
	}

	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__.'/../config/config.php' => config_path('handli.php'),
			], 'handli-config');
		}
	}
}