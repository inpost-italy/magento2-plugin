<?php
declare(strict_types=1);

namespace InPost\Shipment\Service\Quote;

use InPost\Shipment\Api\Data\PointsServiceRequestFactory;
use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Service\Api\PointsApiService;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResourceModel;

class ShippingAddressChanger
{
    const DEFAULT_COUNTRY_ID = 'IT';

    /**
     * @var QuoteResourceModel
     */
    private $quoteResourceModel;

    /**
     * @var PointsApiService
     */
    private $pointsApiService;

    /**
     * @var PointsServiceRequestFactory
     */
    private $pointsServiceRequestFactory;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @param QuoteResourceModel $quoteResourceModel
     * @param PointsApiService $pointsApiService
     * @param PointsServiceRequestFactory $pointsServiceRequestFactory
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        QuoteResourceModel $quoteResourceModel,
        PointsApiService $pointsApiService,
        PointsServiceRequestFactory $pointsServiceRequestFactory,
        RegionFactory $regionFactory
    ) {
        $this->quoteResourceModel = $quoteResourceModel;
        $this->pointsApiService = $pointsApiService;
        $this->pointsServiceRequestFactory = $pointsServiceRequestFactory;
        $this->regionFactory = $regionFactory;
    }

    /**
     * @param Quote $quote
     * @return Quote
     * @throws AlreadyExistsException
     */
    public function setInpostShippingAddress(Quote $quote): Quote
    {
        if ($shippingAddress = $quote->getShippingAddress())
        {
            if ($shippingAddress->getShippingMethod())
            {
                if (strpos($shippingAddress->getShippingMethod(), Inpost::CARRIER_CODE) !== false)
                {
                    $inpostPointId = $shippingAddress->getInpostPointId();
                    $request = $this->pointsServiceRequestFactory->create();
                    $request->setName($inpostPointId);
                    if ($result = $this->pointsApiService->getPoints($request)->getFirstItem()) {
                        $shippingAddress->setCity($result['address_details']['city']);
                        $regionCode = $result['address_details']['province'];
                        $shippingAddress->setRegion($regionCode);
                        $regionId = $this->regionFactory->create()->loadByCode($regionCode, self::DEFAULT_COUNTRY_ID)->getId();
                        $shippingAddress->setRegionId(0);
                        if($regionId)
                        {
                            $shippingAddress->setRegionId($regionId);
                        }
                        $shippingAddress->setStreet(
                            __('c/o') . " " . __('Inpost Collection Point') . ": " . $result['name'] . " " .
                            $result['address_details']['street'] . " " . $result['address_details']['building_number'] . "\n" .
                            $result['opening_hours']
                        );
                        $shippingAddress->setPostcode($result['address_details']['post_code']);
                        $shippingAddress->setVatId(null);
                        $shippingAddress->setCustomerAddressId(null);
                        $shippingAddress->setCompany(null);
                        $this->quoteResourceModel->save($quote);
                    }
                }
            }
        }
        return $quote;
    }
}
