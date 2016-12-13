<?php
namespace Glew\Service\Model\Types;

class Extensions {

    public $extensions = array();

    public function __construct(
        // deprecated this api call
    ) {}

    public function load($pageSize, $pageNum, $sortDir, $filterBy) {
        return $this->extensions;
    }
}
