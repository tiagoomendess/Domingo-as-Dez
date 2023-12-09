<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    /**
     * The attributes that hold geometrical data.
     *
     * @var array
     */
    protected $geometry = array();

    /**
     * Select geometrical attributes as text from database.
     *
     * @var bool
     */
    protected $geometryAsText = false;

    /**
     * Get a new query builder for the model's table.
     * Manipulate in case we need to convert geometrical fields to text.
     *
     * @param  bool  $excludeDeleted
     * @return Builder
     */
    public function newQuery($excludeDeleted = true)
    {
        if (!empty($this->geometry) && $this->geometryAsText === true)
        {
            $raw = '';
            foreach ($this->geometry as $column)
            {
                $raw .= 'AsText(`' . $this->table . '`.`' . $column . '`) as `' . $column . '`, ';
            }
            $raw = substr($raw, 0, -2);
            return parent::newQuery($excludeDeleted)->addSelect('*', DB::raw($raw));
        }
        return parent::newQuery($excludeDeleted);
    }
}
