<?php

namespace Tests;

use BenCarr\Hyperlink\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Statamic\Extend\Manifest;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Statamic;

abstract class TestCase extends OrchestraTestCase
{
    protected Filesystem $files;
    protected $siteFixturePath = __DIR__.'/Fixtures/site';

    protected function getPackageProviders($app)
    {
        return [
            StatamicServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = app(Filesystem::class);
        $this->copyDirectoryFromFixture('resources/blueprints');
        $this->copyDirectoryFromFixture('users');
        $this->copyDirectoryFromFixture('content');
        $this->copyDirectoryFromFixture('storage/app/public');
    }

    protected function getPackageAliases($app)
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make(Manifest::class)->manifest = [
            'bencarr/statamic-hyperlink' => [
                'id' => 'bencarr/statamic-hyperlink',
                'namespace' => 'BenCarr\\Hyperlink',
            ],
        ];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('database.default', null);

        $configs = ['assets', 'cp', 'forms', 'routes', 'static_caching', 'sites', 'stache', 'system', 'users'];
        foreach ($configs as $config) {
            $path = __DIR__."/../vendor/statamic/cms/config/{$config}.php";
            if (file_exists($path)) {
                $app['config']->set("statamic.$config", require($path));
            }
        }

        $files = new Filesystem;
        $files->copyDirectory(__DIR__.'/../vendor/statamic/cms/config', config_path('statamic'));

        $overrides = [
            'statamic/users',
        ];
        foreach($overrides as $path) {
            $files->delete(config_path("{$path}.php"));
            $files->copy("{$this->siteFixturePath}/config/{$path}.php", config_path("{$path}.php"));
            $app['config']->set(str_replace('/', '.', $path), require("{$this->siteFixturePath}/config/{$path}.php"));
        }
    }

    protected function copyDirectoryFromFixture($directory)
    {
        if (base_path($directory)) {
            $this->files->deleteDirectory($directory);
        }
        $this->files->copyDirectory("{$this->siteFixturePath}/{$directory}", base_path($directory));
    }
}
