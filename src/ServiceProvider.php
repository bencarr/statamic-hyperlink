<?php

namespace BenCarr\Hyperlink;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        Fieldtypes\Hyperlink::class,
    ];
    protected $vite = [
        'input' => [
            'resources/js/addon.js',
            'resources/css/addon.css',
        ],
        'publicDirectory' => 'resources/dist',
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
