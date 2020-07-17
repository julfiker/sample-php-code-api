<?php

namespace App\Providers;

use App\Services\Validation\ValidatorExtension;
use Illuminate\Support\ServiceProvider;

class ValidatorExtensionProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->validator->resolver(function($translator, $data, $rules, $messages = [], $customAttributes = [])
        {
            return new ValidatorExtension($translator, $data, $rules, $messages, $customAttributes);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
