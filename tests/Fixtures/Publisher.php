<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Model as Eloquent;
/**
 * @property string name
 */
class Publisher extends Eloquent
{
    protected $table = "publishers";

    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

}
