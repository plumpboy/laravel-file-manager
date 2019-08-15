<?php

namespace Plumpboy\Filemanager\Controllers;

use Illuminate\Http\Request;

use Plumpboy\Filemanager\Repository;
use FileManager;

class FileManagerController extends Controller
{
	protected $repository;

	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}

	/**
     * Get a file and sub-directory in directory. GET
     *
     * @param  Request $request
     * @return json
     */
	public function getItems(Request $request)
	{
		$dirs = $this->repository->getDir($request->dir);
		$sidebar_html = view('laravel-file-manager::sidebar_item')->with([
			'dirs' => $dirs,
			'node' => $request->dir,
		])->render();

		$files = $this->repository->getFile();
		$item_html = view('laravel-file-manager::sidebar_item')->with([
			'files' => $files,
			'dirs' => $dirs,
		])->render();

		$data['node'] = $request->dir;
		$data['item'] = $item_html;
		$data['dir'] = $sidebar_html;

		return [storage_path('app/public'), env('APP_URL').'/storage/app/public'];
	}
	// 	======================
	//	======== file ========
	//	======================

	/**
     * Download a file. GET
     *
     * @param  int $id
     * @return json
     */
	public function downloadFile(int $id)
	{
        return FileManager::download($id);
	}

	/**
     * Upload a file. POST
     *
     * @param  Request $request
     * @return json
     */
	public function uploadFile(Request $request)
	{
		return FileManager::upload($request->files);
	}

	/**
     * Rename a file. PUT/PATCH
     *
     * @param  Request $request
     * @return json
     */
	public function renameFile(Request $request)
	{
		return FileManager::rename($request->id, $request->name);
	}

	/**
     * Move a file. PUT/PATCH
     *
     * @param  Request $request
     * @return json
     */
	public function moveFile()
	{
		return FileManager::move($request->id, $request->path);
	}

	/**
     * Change the visibility of file. PUT/PATCH
     *
     * @param  Request $request
     * @return json
     */
	public function changeVisibility()
	{
		return FileManager::move($request->id, $request->visible);
	}

	/**
     * Delete a file or multiple files. DELETE
     *
     * @param  Request $request
     * @return json
     */
	public function deleteFile()
	{
		return FileManager::move($request->id);
	}

	/*
     * Move a file to another disk. PUT/PATCH
     *
     * @param  Request $request
     * @return json
	 */
	public function moveFileToAnotherDisk()
	{
		return FileManager::move($request->id, $request->diskName);
	}

	// 	=====================
	//	===== directory =====
	//	=====================

	/**
     * Make new directory. POST
     *
     * @param  Request $request
     * @return json
     */
	public function makeDir()
	{
		return FileManager::makeDir($request->id, $request->dirName);
	}

	/**
     * Move directory. PUT/PATCH
     *
     * @param  Request $request
     * @return json
     */
	public function moveDir()
	{
		return FileManager::moveDir($request->id, $request->dirPath);
	}

	/**
     * Rename directory. PUT/PATCH
     *
     * @param  Request $request
     * @return json
     */
	public function renameDir()
	{
		return FileManager::renameDir($request->id, $request->dirPath);
	}

	/**
     * Change the visibility of directory. PUT/PATCH
     *
     * @param  Request $request
     * @return json
     */
	public function changeDirVisibility()
	{
		return FileManager::setDirVisibility($request->id, $request->visible);
	}

	/**
     * Delete directory. DELETE
     *
     * @param  Request $request
     * @return json
     */
	public function deleteDir()
	{
		return FileManager::deleteDir($request->id);
	}
}
