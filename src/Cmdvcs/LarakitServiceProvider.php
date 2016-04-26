<?php

namespace Larakit\Cmdvcs;

use Illuminate\Support\ServiceProvider;

class LarakitServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot() {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->commands([
            CommandVcs::class
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

}
