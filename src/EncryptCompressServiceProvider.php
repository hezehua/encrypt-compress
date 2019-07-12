<?php

namespace Hezehua\EncryptCompress;

use Illuminate\Support\ServiceProvider;

class EncryptCompressServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias(EncryptCompress::class, 'encrypt-compress');
    }

    public function provides()
    {
        return [EncryptCompress::class, 'encrypt-compress'];
    }
}
