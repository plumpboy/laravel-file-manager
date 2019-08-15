<?php

namespace PlumpBoy\FileManager;

use Illuminate\Support\ServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
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
        $this->app->singleton('file-manager', function ($app) {
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
                Image::class,
            ],
            [
                'doc',
                DocFile::class,
            ],
        ] as $driver) {
            $manager->extend($driver[0], function ($app) use ($driver) {
                return $this->app->make($driver[1]);
            });
        }
    }
}
