<?php
namespace Glew\Service\Model\Types;

class Extension {

    /**
     *
     */
    public function __construct() {
        // nothing to inject
    }

    public function parse($extension, $attr)
    {
        $this->name = $extension;
        $this->active = (string) $attr->active;
        $this->version = (string) $attr->version;
        return $this;
    }
}
