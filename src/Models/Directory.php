<?php

namespace Plumpboy\Filemanager\Models;

use Illuminate\Database\Eloquent\Model;
use Plumpboy\Filemanager\Filters\Filterable;

class Directory extends Model
{
	use Filterable;

	const ROOT_ID = 0;

    protected $fillable = [
        'name',
        'parent_id',
        // 'user_id', // ower can modify
        // 'role_id', // if set who has the role can access
        // 'permission_id', // if set who has the permission can access
        'visible', // public or private
    ];

    public function children()
    {
        return $this->hasMany(Directory::class, 'parent_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'dir_id', 'id');
    }

    public function recursiveChildren()
    {
        return $this->children->with([
            'recursiveChildren' => function ($query) {
                $query->select('id');
            },
            'files' => function ($query) {
                $query->select('id');
            },
        ]);
    }
}
