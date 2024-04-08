<?php

namespace InPost\Shipment\Service\Management;

use InPost\Shipment\Api\Data\Shipment;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Api\CreateShipmentService;
use InPost\Shipment\Service\Api\GetShipmentService;
use InPost\Shipment\Service\Builder\DataProcessor\ServiceProcessor;
use InPost\Shipment\Service\Builder\ShipmentRequestBuilder;
use InPost\Shipment\Service\Http\HttpClientException;
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
        ShipmentRepository $shipmentRepository,
        ConfigProvider $configProvider
    ) {
        $this->builder = $builder;
        $this->createShipmentService = $createShipmentService;
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
    public function createShipment(Order $order, string $pointId, $packageOption) : Shipment
    {
        $builder = $this->setupBuilder($order, $packageOption);
        $builder->setTargetPointID($pointId);

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
        if (! $inpostShipment->getTrackingNumber()) {
            throw new \Exception("No InPost tracking number returned");
        }

        return $inpostShipment;
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
        $comment = 'Magento-' . ($this->configProvider->isDebugModeEnabled() ? 'staging' : 'production');
        $builder->setComment($comment);
        $this->serviceProcessor->process($builder);

        return $builder;
    }

}
