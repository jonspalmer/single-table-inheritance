<?php

namespace Nanigans\SingleTableInheritance;

class SingleTableInheritanceObserver {

  public function saving($model) {
    $model->setSingleTableType();
  }
}