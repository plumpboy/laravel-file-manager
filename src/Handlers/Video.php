<?php

namespace Plumpboy\Filemanager\Handlers;

use Illuminate\Support\Facades\Storage;
use App\Libraries\FileManager\HandlerContract;
use App\Models\File as FileUpload;
use DB;
use Illuminate\Http\File as LaravelFile;

class Video extends HandlerContract
{
    protected $rules = 'required|mimes:xls,xlsx,csv';
    protected $type = 'video';

    protected function put($file, $path, $options)
    {
        DB::beginTransaction();
        $this->setUploadData($file, $path);

        if ($this->repository->store($this->data)) {
            if ($this->driver()->putFileAs($path, $file, $this->data['id'], $options)) {
                DB::commit();

                return $this->data['id'];
            } else {
                DB::rollback();

                return false;
            }
        }

        return false;
    }
}
