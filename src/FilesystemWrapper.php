<?php

namespace Plumpboy\Filemanager;

use League\Flysystem\Filesystem;

/**
 * summary
 */
class FilesystemWrapper extends Filesystem
{
    /**
     * summary
     */
    public function getSignedUrl($path, $expiration, array $options = [])
    {
        return $this->getAdapter()->getSignedUrl($path, $expiration, $options);
    }
}
