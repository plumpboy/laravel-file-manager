<?php

namespace Plumpboy\Filemanager;


use InvalidArgumentException;

use Illuminate\Container\Container;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

use Illuminate\Filesystem\FilesystemAdapter;

use League\Flysystem\Filesystem;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory as MemoryStore;

use Aws\S3\S3Client;
use Aws\CloudFront\CloudFrontClient;
use Google\Cloud\Storage\StorageClient;

use Plumpboy\Filemanager\FilesystemWrapper;
use Plumpboy\Filemanager\Adapter\AwsS3Adapter;
use Plumpboy\Filemanager\Adapter\LocalAdapter;
use Plumpboy\Filemanager\Adapter\GoogleStorageAdapter;

use Plumpboy\Filemanager\Repository;
use Plumpboy\Filemanager\Models\File;
use Plumpboy\Filemanager\Models\Directory;
use Plumpboy\Filemanager\Filters\FileFilter;
use Plumpboy\Filemanager\Filters\DirectoryFilter;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Override drivers of filesystems (supported only)
        foreach ($this->app['config']['filesystems.disks'] as $key => $config) {
            if ($config['driver'] == 'gcs') {
                Storage::extend($key, function($app, $config) {
                    $keyFile = array_get($config, 'key_file');
                    if (is_string($keyFile)) {
                        $client = new StorageClient([
                            'projectId' => $config['project_id'],
                            'keyFilePath' => $keyFile,
                        ]);
                    }

                    if (! is_array($keyFile)) {
                        $keyFile = [];
                    }

                    $client = new StorageClient([
                        'projectId' => $config['project_id'],
                        'keyFile' => array_merge(["project_id" => $config['project_id']], $keyFile)
                    ]);

                    $bucket = $storageClient->bucket($config['bucket']);
                    $pathPrefix = array_get($config, 'path_prefix');
                    $storageApiUri = array_get($config, 'storage_api_uri');

                    return $this->adapt($this->createFlysystem(
                        new GoogleStorageAdapter($client, $config['bucket'], $pathPrefix, $storageApiUri), $config
                    ));
                });
            }

            if ($config['driver'] == 's3') {
                Storage::extend($key, function($app, $config) {
                    $config += ['version' => 'latest'];

                    if ($config['key'] && $config['secret']) {
                        $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
                    }

                    $root = $config['root'] ?? null;
                    $options = $config['options'] ?? [];

                    $cloudFront = new CloudFrontClient([
                        'version' => 'latest',
                        'region' => $config['region'],
                        'debug' => config('app.debug'),
                    ]);

                    return $this->adapt($this->createFlysystem(
                        new AwsS3Adapter(new S3Client($config), $cloudFront, $config, $config['bucket'], $root, $options), $config
                    ));
                });
            }

            if ($config['driver'] == 'local') {
                Storage::extend($key, function($app) {
                    $permissions = $config['permissions'] ?? [];

                    $links = ($config['links'] ?? null) === 'skip'
                        ? LocalAdapter::SKIP_LINKS
                        : LocalAdapter::DISALLOW_LINKS;

                    return $this->adapt($this->createFlysystem(new LocalAdapter(
                        $config['root'], $config['lock'] ?? LOCK_EX, $links, $permissions
                    ), $config));
                });
            }
        }

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'laravel-file-manager');

        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'laravel-file-manager');

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/laravel-file-manager'),
        ], 'view');

        $this->publishes([
            __DIR__ . '/resources/js' => resource_path('vendor/laravel-file-manager/js'),
            __DIR__ . '/resources/scss' => resource_path('vendor/laravel-file-manager/scss'),
            __DIR__ . '/resources/image' => resource_path('vendor/laravel-file-manager/image'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/resources/lang' => resource_path('lang/laravel-file-manager'),
        ], 'lang');

        $this->publishes([
            __DIR__ . '/FileController' => app_path('Http/Controller/FileManager'),
        ], 'controller');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerManager();
    }

    /**
     * Register the chatapp manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('filemanager', function ($app) {
            return tap(new FileManager($app), function ($manager) {
                $this->registerHandlers($manager);
            });
        });
    }

    /**
     * Register chatapp handlers.
     *
     * @param  \App\ChatAppNotification\ChatAppManager $manager
     * @return void
     */
    protected function registerHandlers($manager)
    {
        foreach ([
            [
                'image',
                Handlers\Image::class,
            ],
            [
                'doc',
                Handlers\Doc::class,
            ],
            [
                'video',
                Handlers\Video::class,
            ],
        ] as $driver) {
            $manager->extend($driver[0], function ($app) use ($driver) {
                return $this->app->make($driver[1]);
            });
        }
    }

    /**
     * Create a Flysystem instance with the given adapter.
     *
     * @param  \League\Flysystem\AdapterInterface  $adapter
     * @param  array  $config
     * @return \League\Flysystem\FilesystemInterface
     */
    protected function createFlysystem(AdapterInterface $adapter, array $config)
    {
        $cache = Arr::pull($config, 'cache');

        if ($cache) {
            $adapter = new CachedAdapter($adapter, $this->createCacheStore($cache));
        }

        return new FilesystemWrapper($adapter, count($config) > 0 ? $config : null);
    }

    /**
     * Create a cache store instance.
     *
     * @param  mixed  $config
     * @return \League\Flysystem\Cached\CacheInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function createCacheStore($config)
    {
        if ($config === true) {
            return new MemoryStore;
        }

        return new Cache(
            $this->app['cache']->store($config['store']),
            $config['prefix'] ?? 'flysystem',
            $config['expire'] ?? null
        );
    }

    /**
     * Adapt the filesystem implementation.
     *
     * @param  \League\Flysystem\FilesystemInterface  $filesystem
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function adapt(FilesystemInterface $filesystem)
    {
        return new FilesystemAdapter($filesystem);
    }
}
