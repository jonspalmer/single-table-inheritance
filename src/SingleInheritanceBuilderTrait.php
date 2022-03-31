<?php

namespace Nanigans\SingleTableInheritance;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Nanigans\SingleTableInheritance\Contracts\SingleTableInheritanceBuilder;

trait SingleInheritanceBuilderTrait
{
    /**
     * Returns a fresh instance of the model at the root of the inheritance-tree as defined by
     * the Builder's `singleInheritanceRootModelClass` property.
     * @return Model
     */
    protected function newSingleInheritanceRootModel(): Model
    {
        $property = 'singleInheritanceRootModelClass';
        if (!property_exists($this, $property)) {
            throw new \RuntimeException(sprintf(
                '%s must declare a string property named %s',
                get_called_class(),
                $property
            ));
        }

        $className = $this->$property;
        return new $className();
    }

    /**
     * This makes it so that polymorphic collections can be constructed properly.
     *
     * @param  array|string  $columns
     * @return Model[]|static[]
     */
    public function getModels($columns = ['*'])
    {
        /*
         * The problem stems from Laravel using the first element in the collection to hydrate the rest of the records
         * into Eloquent models. Thus, if any of the subsequent records do is of a type that does not inherit
         * from that of the first element, then it fails.
         *
         * If we use an instance of the root model of the
         * inheritance tree, then we can properly hydrate records into polymorphic models.
         */
        $rootModel = $this->newSingleInheritanceRootModel();
        return $rootModel->hydrate(
            $this->query->get($columns)->all()
        )->all();
    }
}
