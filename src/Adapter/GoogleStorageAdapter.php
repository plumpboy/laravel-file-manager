<?php

namespace Plumpboy\Filemanager\Adapter;

use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter as LeagueGoogleStorageAdapter;
use Google\Cloud\Storage\StorageClient;

class GoogleStorageAdapter extends LeagueGoogleStorageAdapter
{
    public function getSignedUrl($path, $expires, array $options = [])
    {
        $object = $this->bucket->object($path);

        $url = $object->signedUrl(new \DateTime('+ ' . $expires . ' seconds'), $options);

        return $url;
    }
}
