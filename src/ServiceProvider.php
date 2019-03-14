<?php

namespace MPorter\VueTemplateLoader;


use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider
 * @package MPorter\VueTemplateLoader
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/vue-loader.php' => config_path('vue-loader.php'),
        ]);
    }
}
