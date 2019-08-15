<?php

namespace Plumpboy\Filemanager;

use Illuminate\Support\Manager;

class FileManager extends Manager
{
    /**
     * Call a custom chatapp handler.
     *
     * @param  string|null $driver
     * @return mixed
     */
    public function handle($driver = null)
    {
        return $this->driver($driver);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefaultDriver()
    {
        return 'image';
    }
}
