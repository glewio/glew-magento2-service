<?php
namespace Glew\Service\Model\Types;

class Customer {

    protected $helper;
    protected $group;
    protected $objectManager;

    /**
     * @param \Magento\Customer\Model\Group $group
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Customer\Model\Group $group,
        \Glew\Service\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->group = $group;
        $this->helper = $helper;
        $this->objectManager = $objectManager;
    }

    public function parse($customer) {

        if (!$customer) {
            return $this;
        }

        $this->id = $customer->getId();
        $this->e_mail = $customer->getData('email');
        $groupId = $customer->getGroupId();
        $customerGroup = $this->group->load($groupId);
        $this->group_id = $groupId;
        $this->group = $customerGroup->getCode();
        $this->created_at = $customer->getCreatedAt();
        $this->updated_at = $customer->getUpdatedAt();
        $this->name = $customer->getName();
        $this->first_name = $customer->getFirstname();
        $this->last_name = $customer->getLastname();
        $this->gender = ((bool) $customer->getGender()) ? $customer->getGender() : '';
        $this->dob = ((bool) $customer->getDob()) ? $this->helper->formatDate($customer->getDob()) : '';
        $this->store = ((bool) $customer->getStore()->getCode()) ? $customer->getStore()->getCode() : '';
        $this->addresses = array();

        $addressParser = $this->objectManager->create('\Glew\Service\Model\Types\Address');
        if ($customer->getDefaultShippingAddress()) {
            $address = $addressParser->parse($customer->getDefaultShippingAddress());
            if ($address) {
                $this->addresses[] = $address;
            }
        }

        return $this;
    }
}
