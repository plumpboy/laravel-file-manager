<?php

namespace Plumpboy\Filemanager\Filters;

use Carbon\Carbon;

class FileFilter extends QueryFilter
{
    /**
     * @param string $type
     */
    public function alphabet(string $type = 'asc')
    {
        $this->builder->orderBy('name', $type);
    }

    /**
     * @param string $type
     */
    public function date(string $type = 'asc')
    {
        $this->builder->orderBy('updated_at', $type);
    }

    /**
     * @param string $type
     */
    public function mimetype(string $type)
    {
        $this->builder->where('type', $type);
    }

    /**
     * @param string $type
     */
    public function disk(string $type)
    {
        $this->builder->where('disk', $type);
    }

    /**
     * @param int $id
     */
    public function dir(int $id)
    {
        $this->builder->where('dir_id', $id);
    }
}
