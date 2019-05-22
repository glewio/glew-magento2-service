<?php
namespace Glew\Service\Model\Types;

class Store
{
    public function parse($store)
    {
        foreach ($store->getData() as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }
}
