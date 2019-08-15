<?php

namespace Plumpboy\Filemanager\Models;

use Illuminate\Database\Eloquent\Model;
use Plumpboy\Filemanager\Filters\Filterable;

class File extends Model
{
    use Filterable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'id', // real name
        'name', // display name and file download name
        'type',

        'bucket', // cloud bucket
        'disk', // disk name
        'extension',
        'dir_id',
        // 'user_id', // ower can modify
        // 'role_id', // if set who has the role can access
        // 'permission_id', // if set who has the permission can access
        'visible', // public or private
    ];

    public function directory()
    {
        return $this->hasOne(Directory::class);
    }
}
