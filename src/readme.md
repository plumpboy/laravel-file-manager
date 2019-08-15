## Upload


```
resolve('filemanager')
->handle('image')
->storage('s3')
->upload($file, $path, $option);
```
**->handle('image')** to specify the driver to handle file upload, 'image' by default, you can write own driver and add it in driver register in provider file

**->storage('s3')** to specify the storage to store uploaded files, 's3' by default.

**->upload($file, $path, $option)**

$file must be instance of \Illuminate\Http\File or \\Illuminate\Http\UploadedFile

You can pass $file as array to upload multiple files.

$path and $option are optional, you can pass $path to special the dictionary of file.

$option based on the storage (i will explain later).

With any storage you can pass ['visibility' => 'public' or 'private'] or just 'public' or 'private' to special the visibility of file.

## Download

You can send a streamed response of file to client like this.

```
$file = File::first();
resolve('filemanager')
->download($file);
```
$file can be \App\Models\File or id
```
$file = File::first();
resolve('filemanager')
->download($file->id, $name, $headers);
```
You also can pass $name to specify the name of download file, and headers of response
## setVisibility, delete, rename, move

```
resolve('filemanager')->setVisibility($file, $visible);

resolve('filemanager')->delete($file); // $file can be array

resolve('filemanager')->rename($file, $newName); // $file can be id

resolve('filemanager')->move($file, $newPath); // $file can be id
```

## Methods of laravel file adapter \Illuminate\Filesystem\FilesystemAdapter
You can feel free to use those methods. Example:
```
resolve('filemanager')->storage($file->storage)->get($file->path);

resolve('filemanager')->storage($file->storage)->files($file->path);

resolve('filemanager')->storage('s3')->directories($path);

...
```

## Add storage driver
Laravel use https://github.com/thephpleague/flysystem in Storage system so you can add driver in suported list or write your own.