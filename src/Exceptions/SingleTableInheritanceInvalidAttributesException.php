<?php

namespace Nanigans\SingleTableInheritance\Exceptions;

class SingleTableInheritanceInvalidAttributesException extends SingleTableInheritanceException {
  protected $invalidAttributes;

  public function __construct($message, array $invalidAttributes) {
    parent::__construct($message . "The attributes: " . implode(',', $invalidAttributes) . " are invalid.");
    $this->invalidAttributes = $invalidAttributes;
  }

  public function getInvalidAttributes() {
    return $this->invalidAttributes;
  }
} 