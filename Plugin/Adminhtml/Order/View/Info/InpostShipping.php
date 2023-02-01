<?php

namespace InPost\Shipment\Plugin\Adminhtml\Order\View\Info;

use InPost\Shipment\Api\Data\PointsServiceRequestFactory;
use Magento\Framework\DataObject;
use \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection as ShipmentCollection;
use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Service\Api\PointsApiService;
use Magento\Sales\Block\Adminhtml\Order\View\Info;
use Psr\Log\LoggerInterface;

class InpostShipping
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    private \InPost\Shipment\Service\Api\PointsApiService $pointsApiService;

    private \InPost\Shipment\Api\Data\PointsServiceRequestFactory $pointsServiceRequestFactory;

    private \InPost\Shipment\Service\Api\GetShipmentService $getShipmentService;

    /**
     * @param LoggerInterface $logger
     * @param PointsApiService $pointsApiService
     * @param PointsServiceRequestFactory $pointsServiceRequestFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \InPost\Shipment\Service\Api\PointsApiService $pointsApiService,
        \InPost\Shipment\Api\Data\PointsServiceRequestFactory $pointsServiceRequestFactory,
        \InPost\Shipment\Service\Api\GetShipmentService $getShipmentService

    ) {
        $this->logger = $logger;
        $this->pointsApiService = $pointsApiService;
        $this->pointsServiceRequestFactory = $pointsServiceRequestFactory;
        $this->getShipmentService = $getShipmentService;
    }


    public function afterToHtml(Info $subject, $result)
    {
        $order = $subject->getOrder();

        // adminhtml_order_shipment_view
        if (!strpos($order->getShippingMethod(), Inpost::CARRIER_CODE) == false) {
            return $result;
        }

        $pointId = $order->getShippingAddress()->getInpostPointId();
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

    private function showCreateShipment(string $pointInfo)
    {
        return'<div class="admin__page-section-item order-payment-method" >
                                <div class="admin__page-section-item-title" >
                    <span class="title">InPost Fullfilment</span>
                <div class="admin__page-section-item-content" >
                      <i><h4>An Inpost shipment has not been created yet</h4></i>
                      Point information:<br>
                      '. $pointInfo .'
                </div>
            </div>
            </div>
            ';
    }

    private function shipmentCreateForm(string $addressInformation)
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
            '. $addressInformation . '
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
        
        return $this->formatHtml($points->getFirstItem());
    }

    private function formatHtml(\InPost\Shipment\Api\Data\PointData $pointData)
    {
        $addressDetails = $pointData->getAddressDetails();

        return "<i>Point ID:</i> <b>{$pointData->getName()}</b><br>
            <i>Name:</i> {$pointData->getLocationDescription()},{$pointData->getLocationDescription1()}<br>
            <i>Address:</i> {$addressDetails['street']} {$addressDetails['building_number']} </b><br>
            ";
    }

    private function hasHandle(\Magento\Sales\Block\Adminhtml\Order\View\Info $block, $handle)
    {
        return in_array($handle, $block->getLayout()->getUpdate()->getHandles());
    }

    private function showShipmentTracking(ShipmentCollection $getShipmentsCollection)
    {
        $shipmentInformation = '';
        foreach ($getShipmentsCollection as $shipment) {
            foreach ($shipment->getTracks() as $track) {
                try {
                    $inpostShipment = $this->getShipmentService->getShipment($track->getTrackNumber());
                    $inpostShipment = new DataObject($inpostShipment['items'][0]);

                    $shipmentInformation .= "<i>Shipment ID:</i> <b>{$inpostShipment->getId()}</b><br>
            <i>Status: </i> <b>{$inpostShipment->getStatus()}</b><br>
            <i>Service: </i> {$inpostShipment->getService()}<br>
            <i>Created: </i> {$inpostShipment->getCreatedAt()}<br>
            ";
                } catch (\Exception $exception) {
                    $shipmentInformation .= "Error occured during fetching shipment:" . $exception->getMessage();
                }
            }
        }

        return'<div class="admin__page-section-item order-payment-method" >
                                <div class="admin__page-section-item-title" >
                    <span class="title">InPost Fullfilment</span>
                <div class="admin__page-section-item-content" >
                      Point information:<br>
                      '. $shipmentInformation .'
                </div>
            </div>
            </div>
            ';
    }
}