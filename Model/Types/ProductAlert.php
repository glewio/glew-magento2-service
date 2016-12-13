<?php
namespace Glew\Service\Model\Types;

class ProductAlert {

    public function parse($alert) {

        $this->id = $alert->getAlertStockId();
        $this->customer_id = $alert->getCustomerId();
        $this->product_id = $alert->getProductId();
        $this->created_at = $alert->getAddDate();

        return $this;
        
    }
}
