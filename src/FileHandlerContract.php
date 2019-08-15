<?php

namespace PlumpBoy\FileManager;

use Illuminate\Validation\ValidationException;
use PlumpBoy\FileManager\File;
use Validator;
use DB;

abstract class FileHandlerContract
{
    /**
     * Storage instance.
     *
     * @var string
     */
    protected $storage;

    /**
     * Storage driver name.
     *
     * @var string
     */
    protected $driver;

    /**
     * file data.
     *
     * @var string
     */
    protected $data = [];

    /**
     * Constructor.
     *
     * @param $apiKey
     * @param string $method
     */
    public function __construct()
    {
        $this->storage = resolve('filesystem');
        $this->driver = $this->getDefaultDriver();
    }

    public function upload($uploadFile, $path = '/', $options = [])
    {
        $this->validate($uploadFile);
        $path = $this->setPath($path);

        if (is_array($uploadFile)) {
            foreach ($uploadFile as $file) {
                $this->put($file, $path, $options);
            }
        } else {
            $this->put($uploadFile, $path, $options);
        }
    }

    public function setVisibility($file, $visible)
    {
        if (![$file, $path] = $this->getFileInfo($file)) {
            return false;
        }

        return $this->driver()->setVisibility($path, $visible);
    }

    public function download($file, $name = null, $headers = [])
    {
        if (![$file, $path] = $this->getFileInfo($file)) {
            return false;
        }

        if ($name == null) {
            $name = $file->name;
        }

        return $this->driver()->download($path, $name, $headers);
    }

    public function delete($files)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                if (![$file, $path] = $this->getFileInfo($file)) {
                    return false;
                }

                if (!$this->driver()->delete($paths)) {
                    return false;
                }
            }

            return true;
        }

        if (![$file, $path] = $this->getFileInfo($files)) {
            return false;
        }

        return $this->driver()->delete($path);
    }

    public function rename($file, $name)
    {
        if (!($file instanceof FileUpload)) {
            if (!$file = $this->getFile($file)) {
                return false;
            }
        }

        return File::update($file->id, ['name' => $name]);
    }

    public function move($file, $newPath)
    {
        if (![$file, $path] = $this->getFileInfo($file)) {
            return false;
        }

        DB::beginTransaction();
        if (File::update($file->id, ['path' => $newPath])) {
            if ($this->driver()->move($this->getAbsolutePath($file), $this->setPath($newPath) . $file->id)) {
                DB::commit();

                return true;
            } else {
                DB::rollback();
            }
        } else {
            return false;
        }
    }

    public function storage($name)
    {
        $this->driver = $name;
        $this->setData();

        return $this;
    }

    public function getFileInfo($file)
    {
        if (!($file instanceof FileUpload)) {
            if (!$file = $this->getFile($file)) {
                return false;
            }
        }

        $this->storage($file->storage);
        $path = $this->getAbsolutePath($file);

        return [$file, $path];
    }

    protected function getFile($id)
    {
        return File::findById($id);
    }

    protected function getAbsolutePath($file)
    {
        return $this->setPath($file->path) . $file->id;
    }

    protected function setPath($path)
    {
        if (substr($path, -1) != '/') {
            $path .= '/';
        }

        return $path;
    }

    protected function validate($file)
    {
        if (is_array($file)) {
            $rules = [
                'file.*' => $this->rules,
            ];
        } else {
            $rules = [
                'file' => $this->rules,
            ];
        }

        $validator = Validator::make(['file' => $file], $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    protected function generateUuidName()
    {
        return uniqid(str_replace('.', '_', microtime(true)) . '_');
    }

    protected function generateId($file)
    {
        return $this->generateUuidName() . '.' . $file->getClientOriginalExtension();
    }

    protected function setData()
    {
        $this->data['bucket'] = config('filesystems.disks.' . $this->driver . '.bucket') ? config('filesystems.disks.' . $this->driver . '.bucket') : null;
        $this->data['storage'] = $this->driver;
    }

    protected function setUploadData($uploadFile, $path)
    {
        $this->data['id'] = $this->generateId($uploadFile);
        $this->data['name'] = $uploadFile->getClientOriginalName();
        $this->data['path'] = $path;
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return config('filesystems.default');
    }

    protected function driver()
    {
        return $this->storage->disk($this->driver);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->storage->disk($this->driver)->$method(...$parameters);
    }
}