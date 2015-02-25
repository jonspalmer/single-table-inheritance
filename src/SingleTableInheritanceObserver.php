<?php

namespace Phaza\SingleTableInheritance;

class SingleTableInheritanceObserver {

  public function saving($model) {
    $model->filterPersistedAttributes();
    $model->setSingleTableType();
  }
}
