<?php

namespace PlumpBoy\FileManager;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'name',
        'type',
        'path',
        'bucket',
        'storage',
    ];

    public $timestamps = false;
}

