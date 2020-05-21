<?php

namespace Nanigans\SingleTableInheritance\Exceptions;

class SingleTableInheritanceWrongInheritanceException extends SingleTableInheritanceException {

    public function __construct($message, $subClass, $parentClass) {
        parent::__construct($message . " Subclass: $subClass | Parent: $parentClass");
    }

} 