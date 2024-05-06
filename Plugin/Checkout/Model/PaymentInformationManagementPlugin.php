<?php
declare(strict_types=1);

namespace InPost\Shipment\Plugin\Checkout\Model;

use InPost\Shipment\Service\Quote\ShippingAddressChanger;
use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote;

class PaymentInformationManagementPlugin
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ShippingAddressChanger
     */
    private $shippingAddressChanger;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param ShippingAddressChanger $shippingAddressChanger
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        ShippingAddressChanger $shippingAddressChanger
    )
    {
        $this->cartRepository = $cartRepository;
        $this->shippingAddressChanger = $shippingAddressChanger;
    }

    /**
     * @param PaymentInformationManagement $subject
     * @param int $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     */
    public function beforeSavePaymentInformation
    (
        PaymentInformationManagement $subject,
        int $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    )
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $this->shippingAddressChanger->setInpostShippingAddress($quote);
    }

}
