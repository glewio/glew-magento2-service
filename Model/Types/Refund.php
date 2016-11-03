<?php
namespace Glew\Service\Model\Types;

class Refund {

    public function parse($refund) {
        $this->refund_id = $refund->getId();
        $this->order_id = $refund->getData('order_id');
        $this->amount = $refund->getData('grand_total');
        $this->created_at = $refund->getCreatedAt();
        $this->updated_at = $refund->getUpdatedAt();

        return $this;
    }
}
