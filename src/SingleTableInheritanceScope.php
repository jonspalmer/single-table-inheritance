<?php

namespace Nanigans\SingleTableInheritance;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SingleTableInheritanceScope implements Scope {

  /**
   * Apply the scope to a given Eloquent query builder.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $builder
   * @return void
   */
  public function apply(Builder $builder, Model $model) {
    $subclassTypes = array_keys($model->getSingleTableTypeMap());

    if (!empty($subclassTypes) ) {
      $builder->whereIn($model->getQualifiedSingleTableTypeColumn(), $subclassTypes);
    }
  }

  /**
   * Remove the scope from the given Eloquent query builder.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $builder
   * @return void
   */
  public function remove(Builder $builder, Model $model) {
    $column = $model->getQualifiedSingleTableTypeColumn();

    $query = $builder->getQuery();

    $bindings = $query->getRawBindings()['where'];
    foreach ((array) $query->wheres as $key => $where) {
      // If the where clause is a single table inheritance in constraint, we will remove it from
      // the query and reset the keys on the wheres. This allows this developer to
      // include model in a relationship result set that is lazy loaded.

      if ($this->isSingleTableInheritanceConstraint($where, $column)) {
        unset($query->wheres[$key]);

        // Assume (naively) that no other scope is binding the same values as the values of our in query.
        // Not perfect but its about the best we can do without they query keeping better track of which bindings
        // belong to which where clause.
        foreach($where['values'] as $value) {
          if (($binding_key = array_search($value, $bindings)) >= 0) {
            unset($bindings[$binding_key]);
          }
        }

        $query->setBindings(array_values($bindings));
        $query->wheres = array_values($query->wheres);
      }
    }
  }

  /**
   * Determine if the given where clause is a single table inheritance constraint.
   *
   * @param  array   $where
   * @param  string  $column
   * @return bool
   */
  protected function isSingleTableInheritanceConstraint(array $where, $column)
  {
    return $where['type'] == 'In' && $where['column'] == $column;
  }
}