<?php
namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Nanigans\SingleTableInheritance\Tests\Fixtures\UsesUuid;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
* @property string                 name
* @property Publisher              publisher
*/
class Publication extends Eloquent
{
    use SingleTableInheritanceTrait;
    protected static $singleTableTypeField = 'type';

    protected $table = "publications";

    protected static $singleTableSubclasses = [
        'Nanigans\SingleTableInheritance\Tests\Fixtures\Book',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'publisher_id',
        'type',
        'name'
    ];

    protected $attributes = [
        'publisher_id' => '',
        'name' => ''
    ];
    protected $casts = [
        'name' => 'string',
        'publisher_id' => 'string',
        ];

    public function publisher()
    {
        return $this->belongsTo(
            Publisher::class,
            'publisher_id',
            'id'
        );
    }

    public function setPublisherIdAttribute($value)
    {
        $this->attributes['publisher_id'] = $value . "test";
    }

}
