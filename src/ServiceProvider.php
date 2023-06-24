<?php

namespace BenCarr\Hyperlink;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        Fieldtypes\Hyperlink::class,
    ];
    protected $vite = [
        'hotFile' => __DIR__.'/../vite.hot',
        'publicDirectory' => 'dist',
        'input' => [
            'resources/js/addon.js',
            'resources/css/addon.css',
        ],
    ];

    public function bootAddon()
    {
        $this->publishes([
            __DIR__.'/../config/statamic/hyperlink.php' => config_path('statamic/hyperlink.php'),
        ], 'hyperlink-config');
    }

    public function register()
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../config/statamic/hyperlink.php', 'statamic.hyperlink');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'hyperlink');
    }
}
