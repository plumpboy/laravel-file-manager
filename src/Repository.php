<?php

namespace Plumpboy\Filemanager;

use Plumpboy\Filemanager\Models\File;
use Plumpboy\Filemanager\Models\Directory;
use Plumpboy\Filemanager\Filters\FileFilter;
use Plumpboy\Filemanager\Filters\DirectoryFilter;
use Illuminate\Support\Arr;

/**
 * summary
 */
class Repository
{
	protected $file;

	protected $fileFilter;

	protected $directory;

    /**
     * summary
     */
    public function __construct(File $file, Directory $directory, FileFilter $fileFilter, DirectoryFilter $directoryFilter)
    {
    	$this->file = $file;
    	$this->fileFilter = $file->filter($fileFilter);
    	$this->directory = $directory;
    	$this->directoryFilter = $directory->filter($directoryFilter);
    }

    ////////////
	/// File ///
	////////////
    public function getFile()
	{
		return $this->fileFilter->get();
	}

	public function addFile(array $data)
	{
		return $this->file->create($data);
	}

	public function renameFile(int $id, string $name)
	{
		return $this->update($id, ['name' => $name]);
	}

	public function moveFile(int $id, int $new_directory_id)
	{
		return $this->update($id, ['directory_id' => $new_directory_id]);
	}

	public function updateFile(int $id, array $data)
	{
		Arr::forget($data, 'id');

		return $this->file->where('id', $id)->update($data);
	}

	public function deleteFile(mixed $ids)
	{
		return $this->file->destroy($ids);
	}

	//////////////////
	/// directory ///
	//////////////////

	public function getDir(int $parent_id = directory::ROOT_ID)
	{
		return $this->directory->where('parent_id', $parent_id)->get();
	}

	public function getSubTree($id)
	{
		return $this->directory->recursiveChildren()->find($id);
	}

	public function makeDir(array $data, int $parent_id = directory::ROOT_ID)
	{
		$data = array_merge($data, ['parent_id' => $parent_id]);

		return $this->directory->create($data);
	}

	public function renameDir(int $id, string $name)
	{
		return $this->updateDir($id, ['name' => $name]);
	}

	public function moveDir(int $id, int $new_parent_id)
	{
		return $this->updateDir($id, ['parent_id' => $new_parent_id]);
	}

	public function updateDir(int $id, array $data)
	{
		return $this->directory->where('id', $id)->update($data);
	}

	public function deleteDir(int $id)
	{
		return $this->directory->where('id', $id)->delete();
	}
}
