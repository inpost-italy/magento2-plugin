<?php

namespace InPost\Shipment\Service\Management;

use InPost\Shipment\Api\Data\Shipment;
use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Api\CreateShipmentService;
use InPost\Shipment\Service\Api\GetShipmentService;
use InPost\Shipment\Service\Builder\DataProcessor\ServiceProcessor;
use InPost\Shipment\Service\Builder\ShipmentRequestBuilder;
use InPost\Shipment\Service\Http\HttpClientException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment as OrderShipment;
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

    /** @var ConfigProvider */
    private $configProvider;

    /** @var GetShipmentService */
    private $getShipmentService;
    private ServiceProcessor $serviceProcessor;

    /**
     * @param ShipmentRequestBuilder $builder
     * @param CreateShipmentService $createShipmentService
     * @param GetShipmentService $getShipmentService
     * @param TrackFactory $trackFactory
     * @param ShipmentRepository $shipmentRepository
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        ShipmentRequestBuilder $builder,
        CreateShipmentService $createShipmentService,
        GetShipmentService $getShipmentService,
        ServiceProcessor $serviceProcessor,
        TrackFactory $trackFactory,
        ShipmentRepository $shipmentRepository,
        ConfigProvider $configProvider
    ) {
        $this->builder = $builder;
        $this->createShipmentService = $createShipmentService;
        $this->trackFactory = $trackFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->getShipmentService = $getShipmentService;
        $this->configProvider = $configProvider;
        $this->serviceProcessor = $serviceProcessor;
    }

    /**
     * @param OrderShipment $shipment
     * @param $packageOption
     *
     * @return void
     * @throws HttpClientException
     * @throws \Exception
     */
    public function createShipment(OrderShipment $shipment, $packageOption)
    {
        $order = $shipment->getOrder();
        $builder = $this->setupBuilder($order, $packageOption);

        // Create shipment in ShipX system
        $createShipmentResult = $this->createShipmentService->execute($builder->build());

        // Sleep for 1 second to make sure that the shipment is created in ShipX system
        sleep(1);

        // Get shipment from ShipX system
        $inpostShipmentResponse = $this->getShipmentService->getShipmentById($createShipmentResult['id']);
        if ($inpostShipmentResponse->isEmpty()) {
            throw new \Exception("Unable to create InPost shipment. Created shipment is not found.");
        }

        $inpostShipment = $inpostShipmentResponse->getFirstItem();
        $trackingNumber = $inpostShipment->getTrackingNumber();
        if (!$trackingNumber) {
            throw new \Exception("No inpost tracking number returned");
        }

        $this->addTrack($shipment, $inpostShipment);
        $this->addTrackingNumberToOrderShipment($shipment, (string)$inpostShipment->getId());
    }

    /**
     * @param Order $order
     * @param $packageOption
     *
     * @return ShipmentRequestBuilder
     */
    private function setupBuilder(Order $order, $packageOption): ShipmentRequestBuilder
    {
        $builder = $this->builder;
        $builder->setOrder($order);
        $builder->setSender([
            'company_name' => $this->configProvider->getCompanyName(),
            'email' => $this->configProvider->getEmail(),
            'phone' => $this->configProvider->getMobilePhoneNumber(),
        ]);
        $builder->setParcels(['template' => $packageOption]);
        $this->serviceProcessor->process($builder);

        return $builder;
    }

    /**
     * @param OrderShipment $shipment
     * @param string $trackingNumber
     *
     * @return void
     * @throws CouldNotSaveException
     */
    private function addTrackingNumberToOrderShipment(OrderShipment $shipment, string $trackingNumber)
    {
        $shipment->setData('inpost_shipment_id', $trackingNumber);
        $this->shipmentRepository->save($shipment);
    }

    /**
     * @param OrderShipment $shipment
     * @param Shipment $inpostShipment
     *
     * @return void
     */
    private function addTrack(OrderShipment $shipment, Shipment $inpostShipment)
    {
        $trackData = [
            'carrier_code' => Inpost::CARRIER_CODE,
            'title' => $inpostShipment->getService(),
            'number' => $inpostShipment->getTrackingNumber()
        ];

        $track = $this->trackFactory->create()->addData($trackData);
        $shipment->addTrack($track);
    }
}
