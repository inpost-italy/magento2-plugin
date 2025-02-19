<?php
declare(strict_types=1);

namespace InPost\Shipment\Plugin\Checkout\Model;

use InPost\Shipment\Service\Quote\ShippingAddressChanger;
use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestPaymentInformationManagementPlugin
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var ShippingAddressChanger
     */
    private $shippingAddressChanger;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param ShippingAddressChanger $shippingAddressChanger
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository,
        ShippingAddressChanger $shippingAddressChanger
    )
    {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->shippingAddressChanger = $shippingAddressChanger;
    }

    /**
     * @param GuestPaymentInformationManagement $subject
     * @param string $cartId
     * @param string $email
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @return void
     */
    public function beforeSavePaymentInformation(
        GuestPaymentInformationManagement $subject,
        string $cartId,
        string $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {

        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($quoteIdMask->getQuoteId());
        $this->shippingAddressChanger->setInpostShippingAddress($quote);
    }
}
