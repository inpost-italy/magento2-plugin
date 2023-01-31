<?php
declare(strict_types=1);

namespace InPost\Shipment\Plugin\Checkout\Model;

use InPost\Shipment\Api\Data\PointsServiceRequestFactory;
use InPost\Shipment\Service\Api\PointsApiService;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\Exception\InputException;

class ShippingInformationManagementPlugin
{
    /** @var PointsApiService */
    private $pointsApiService;

    /** @var PointsServiceRequestFactory */
    private $pointsServiceRequestFactory;

    /**
     * @param PointsApiService $pointsApiService
     * @param PointsServiceRequestFactory $pointsServiceRequestFactory
     */
    public function __construct(
        PointsApiService $pointsApiService,
        PointsServiceRequestFactory $pointsServiceRequestFactory
    ) {
        $this->pointsApiService = $pointsApiService;
        $this->pointsServiceRequestFactory = $pointsServiceRequestFactory;
    }

    /**
     * Set Inpost Attribute Values
     *
     * @param ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     * @throws InputException
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ): array {
        if ($extAttributes = $addressInformation->getExtensionAttributes()) {
            $inpostPointId = $extAttributes->getInpostPointId();

            if (!$inpostPointId && $addressInformation->getShippingCarrierCode() === \InPost\Shipment\Carrier\Inpost::CARRIER_CODE) {
                throw new InputException(
                    __('Cannot proceed checkout without selected Inpost Point.')
                );
            }

            $request = $this->pointsServiceRequestFactory->create();
            $request->setName($inpostPointId);
            if ($this->pointsApiService->getPoints($request)->isEmpty()) {
                throw new InputException(
                    __('Something went wrong. Inpost Point was not found.')
                );
            }

            $addressInformation->getShippingAddress()->setInpostPointId($inpostPointId);
        }

        return [$cartId, $addressInformation];
    }
}
