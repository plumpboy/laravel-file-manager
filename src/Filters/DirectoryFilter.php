<?php

namespace Plumpboy\Filemanager\Filters;

use Carbon\Carbon;

class DirectoryFilter extends QueryFilter
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
}
