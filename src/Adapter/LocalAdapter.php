<?php

namespace Plumpboy\Filemanager\Adapter;

use League\Flysystem\Adapter\Local as LeagueLocalAdapter;

class LocalAdapter extends LeagueLocalAdapter
{
    public function getSignedUrl($path, $expires, array $options = [])
    {
        return URL::temporarySignedRoute('local.security_file', now()->addSeconds($expires), [
            'filePath' => $path,
        ]);
    }

    public function getUrl($path)
    {
        return $this->applyPathPrefix($path);
    }
}
