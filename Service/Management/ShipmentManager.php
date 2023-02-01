<?php

namespace InPost\Shipment\Service\Management;

use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Service\Api\CreateShipmentService;
use InPost\Shipment\Service\Builder\ShipmentRequestBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Sales\Model\Order\ShipmentRepository;

class ShipmentManager
{
    /**
     * @var ShipmentRequestBuilder
     */
    private $builder;

    /**
     * @var CreateShipmentService
     */
    private $createShipmentService;

    /**
     * @var TrackFactory
     */
    private $trackFactory;

    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    public function __construct(
        ShipmentRequestBuilder $builder,
        CreateShipmentService $createShipmentService,
        TrackFactory $trackFactory,
        ShipmentRepository $shipmentRepository
    ) {
        $this->builder = $builder;
        $this->createShipmentService = $createShipmentService;
        $this->trackFactory = $trackFactory;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @param Shipment $shipment
     *
     * @return void
     * @throws \Exception
     */
    public function createShipment(Shipment $shipment, $packageOption)
    {
        $order = $shipment->getOrder();
        $address = $order->getShippingAddress();

        $this->builder->setReceiver([
            'first_name' => $order->getCustomerFirstname(),
            'last_name' => $order->getCustomerFirstname(),
            'email' => $order->getCustomerEmail(),
            'phone' => "500355111",
            'address' => [
                'city' => $address->getCity(),
                'post_code' => $address->getPostcode(),
                'street' => $address->getStreet()[0],
                // @TODO FIX
                'building_number' => '1',
            ],
        ]);

        $this->builder->setParcels([
            'template' => $packageOption
        ]);

        $this->builder->setService('inpost_locker_standard');
        $this->builder->setCustomAttributes([
//            'target_point' => $address->getInpostPointId()
            'target_point' => 'ITARC23379'
        ]);


        $request = $this->builder->build();
        $shipmentCreationResponse = $this->createShipmentService->execute($request);

        $this->addTrack(
            $shipment,
            $shipmentCreationResponse['service'],
            $shipmentCreationResponse['id']
        );
    }

    /**
     * @param Shipment $shipment
     * @param $service
     * @param $trackId
     *
     * @return void
     * @throws CouldNotSaveException
     */
    private function addTrack(Shipment $shipment, $service, $trackId)
    {
        $data = array(
            'carrier_code' => Inpost::CARRIER_CODE,
            'title' => $service,
            'number' => $trackId,
        );

        $track = $this->trackFactory->create()->addData($data);
        $shipment->addTrack($track);

        $this->shipmentRepository->save($shipment);
    }
}