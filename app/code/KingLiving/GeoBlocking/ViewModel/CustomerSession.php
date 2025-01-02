<?php
declare(strict_types=1);

namespace KingLiving\GeoBlocking\ViewModel;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/*
 * Customer Session
 */
class CustomerSession implements ArgumentInterface
{
    private SessionFactory $customerSessionFactory;

    /**
     * @param SessionFactory $customerSessionFactory
     */
    public function __construct(
        SessionFactory $customerSessionFactory
    ) {
        $this->customerSessionFactory = $customerSessionFactory;
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn(): bool
    {
        $customer = $this->customerSessionFactory->create();
        return $customer->isLoggedIn();
    }
}
