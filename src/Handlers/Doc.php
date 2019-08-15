<?php

namespace Plumpboy\Filemanager\Handlers;

use Illuminate\Support\Facades\Storage;
use App\Libraries\FileManager\HandlerContract;
use App\Models\File;
use DB;
use Illuminate\Http\File as LaravelFile;

class Doc extends HandlerContract
{
    protected $rules = 'required|mimes:xls,xlsx,csv';
    protected $type = 'doc';

    protected function put($file, $path, $options)
    {
        $this->setUploadData($file, $path);

        if ($this->driver()->putFileAs($path, $file, $this->data['id'], $options)) {
            return File::create($this->data);
        }

        return false;
    }
}
