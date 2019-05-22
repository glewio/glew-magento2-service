<?php
namespace Glew\Service\Model\Types;

use Magento\Customer\Model\Group;
use Glew\Service\Helper\Data;
use Glew\Service\Model\Types\AddressFactory;

class Customer
{
    protected $helper;
    protected $group;
    protected $addressFactory;

    /**
     * @param \Magento\Customer\Model\Group $group
     * @param \Glew\Service\Helper\Data $helper
     * @param \Glew\Service\Model\Types\AddressFactory $addressFactory
     */
    public function __construct(
        Group $group,
        Data $helper,
        AddressFactory $addressFactory
    ) {
        $this->group = $group;
        $this->helper = $helper;
        $this->addressFactory = $addressFactory;
    }

    public function parse($customer)
    {
        $addressParser = $this->addressFactory->create();

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

        $addressParser = $this->addressFactory->create();
        if ($customer->getDefaultShippingAddress()) {
            $address = $addressParser->parse($customer->getDefaultShippingAddress());
            if ($address) {
                $this->addresses[] = $address;
            }
        }

        return $this;
    }
}
