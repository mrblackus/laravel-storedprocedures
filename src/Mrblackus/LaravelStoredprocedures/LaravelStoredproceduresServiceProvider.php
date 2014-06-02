<?php

namespace Mrblackus\LaravelStoredprocedures;

use Illuminate\Support\ServiceProvider;

class LaravelStoredproceduresServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('mrblackus/laravel-storedprocedures');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app['command.generate-sp'] = $this->app->share(function($app)
        {
            return new GenerateSpCommand;
        });
        $this->commands('command.generate-sp');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
