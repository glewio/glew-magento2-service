<?php
namespace Glew\Service\Model\Types;

class Subscriber
{
    public function parse($subscriber)
    {
        foreach ($subscriber->getData() as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }
}
