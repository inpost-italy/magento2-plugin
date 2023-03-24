<?php

namespace InPost\Shipment\Plugin\Adminhtml\Order\View\Info;

use InPost\Shipment\Api\Data\PointsServiceRequestFactory;
use InPost\Shipment\Service\Api\GetShipmentService;
use Magento\Framework\DataObject;
use \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection as ShipmentCollection;
use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Service\Api\PointsApiService;
use Magento\Sales\Block\Adminhtml\Order\View\Info;
use Psr\Log\LoggerInterface;

class InpostShipping
{
    /** @var LoggerInterface */
    private $logger;

    /** @var PointsApiService */
    private $pointsApiService;

    /** @var PointsServiceRequestFactory */
    private $pointsServiceRequestFactory;

    /** @var GetShipmentService */
    private $getShipmentService;

    /**
     * @param LoggerInterface $logger
     * @param PointsApiService $pointsApiService
     * @param PointsServiceRequestFactory $pointsServiceRequestFactory
     * @param GetShipmentService $getShipmentService
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        PointsApiService $pointsApiService,
        PointsServiceRequestFactory $pointsServiceRequestFactory,
        GetShipmentService $getShipmentService

    ) {
        $this->logger = $logger;
        $this->pointsApiService = $pointsApiService;
        $this->pointsServiceRequestFactory = $pointsServiceRequestFactory;
        $this->getShipmentService = $getShipmentService;
    }


    /**
     * @param Info $subject
     * @param $result
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(Info $subject, $result)
    {
        $order = $subject->getOrder();

        // If we don't have an Inpost shipping method
        if (strpos($order->getShippingMethod(), Inpost::CARRIER_CODE) === false) {
            return $result;
        }


        if (!$pointId = $order->getShippingAddress()->getInpostPointId()) {
            return $result;
        }

        $addressInformation = $this->getInpostPointInfo($pointId);

        if ($this->hasHandle($subject, 'sales_order_view')) {
            $result .= $order->getShipmentsCollection()->count()
                ? $this->showShipmentTracking($order->getShipmentsCollection())
                : $this->showCreateShipment($addressInformation);
        }

        if ($this->hasHandle($subject, 'adminhtml_order_shipment_new')) {
            $result .= $this->shipmentCreateForm($addressInformation);
        }


        return $result;
    }

    private function showCreateShipment(string $pointInfo): string
    {
        return '<div class="admin__page-section-item order-payment-method" >
                                <div class="admin__page-section-item-title" >
                    <span class="title">InPost Fullfilment</span>
                <div class="admin__page-section-item-content" >
                      <i><h4>An Inpost shipment has not been created yet</h4></i>
                      Point information:<br>
                      ' . $pointInfo . '
                </div>
            </div>
            </div>
            ';
    }

    private function shipmentCreateForm(string $addressInformation): string
    {
        return '<div class="admin__page-section-item order-payment-method" >
                                <div class="admin__page-section-item-title" >
                    <span class="title">InPost Fullfilment</span>
                </div>
                <div class="admin__page-section-item-content" >
                   <table class="data-table admin__control-table" id="inpost_package_size">
        <thead>
        <tr class="headings">
            <th class="col-carrier">Select package type</th>
            <th class="col-info">Pickup point information</th>
        </tr>
        </thead>
        <tfoot>
      
        </tfoot>
        <tbody id="track_row_container">
        <tr>
        <td class="col-carrier" style="width: 30%">
            <select name="inpost[package_type]" id="inpost_package" class="select admin__control-select" required>
                                    <option selected value="">Select package option</option>
                                    <option value="small">InPost S</option>
                                    <option value="medium">InPost M</option>
                                    <option value="large">InPost L</option>
                            </select>
        </td>
        <td class="col-title">
            ' . $addressInformation . '
        </td>   
        </tr></tbody>
        </table>
                </div>
            </div>
            </div>
            ';
    }

    /**
     * @param string $pointId
     *
     * @return string
     */
    private function getInpostPointInfo(string $pointId): string
    {
        $inpostPointRequest = $this->pointsServiceRequestFactory->create();
        $inpostPointRequest->setName($pointId);
        $points = $this->pointsApiService->getPoints($inpostPointRequest);

        return $points->getItemsCount() ? $this->formatHtml($points->getFirstItem()) : 'No point information';
    }

    /**
     * @param \InPost\Shipment\Api\Data\PointData $pointData
     * @return string
     */
    private function formatHtml(\InPost\Shipment\Api\Data\PointData $pointData): string
    {
        $addressDetails = $pointData->getAddressDetails();

        return "<i>Point ID:</i> <b>{$pointData->getName()}</b><br>
            <i>Name:</i> {$pointData->getLocationDescription()},{$pointData->getLocationDescription1()}<br>
            <i>Address:</i> {$addressDetails['street']} {$addressDetails['building_number']} </b><br>
            ";
    }

    /**
     * @param Info $block
     * @param $handle
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function hasHandle(Info $block, $handle): bool
    {
        return in_array($handle, $block->getLayout()->getUpdate()->getHandles());
    }

    /**
     * @param ShipmentCollection $getShipmentsCollection
     * @return string
     */
    private function showShipmentTracking(ShipmentCollection $getShipmentsCollection): string
    {
        $shipmentInformation = '';
        foreach ($getShipmentsCollection as $shipment) {
            foreach ($shipment->getTracks() as $track) {
                try {
                    $inpostShipment = $this->getShipmentService->getShipmentByTrackingId($track->getTrackNumber());
                    $inpostShipment = new DataObject($inpostShipment['items'][0]);
                    $shipmentInformation .= "<i>Tracking ID:</i> <b>{$track->getTrackNumber()}</b><br>
            
            <i>Status: </i> <b>{$inpostShipment->getStatus()}</b><br>
            <i>Service: </i> {$inpostShipment->getService()}<br>
            <i>Created: </i> {$inpostShipment->getCreatedAt()}<br>
            ";
                } catch (\Exception $exception) {
                    $shipmentInformation .= "Error occured during fetching shipment:" . $exception->getMessage();
                }
            }
        }

        return '<div class="admin__page-section-item order-payment-method" >
                                <div class="admin__page-section-item-title" >
                    <span class="title">InPost Fullfilment</span>
                <div class="admin__page-section-item-content" >
                      Point information:<br>
                      ' . $shipmentInformation . '
                </div>
            </div>
            </div>
            ';
    }
}
