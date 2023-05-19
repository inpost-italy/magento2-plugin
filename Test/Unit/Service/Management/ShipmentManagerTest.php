<?php

namespace InPost\Shipment\Test\Unit\Service\Management;

use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Api\CreateShipmentService;
use InPost\Shipment\Service\Api\GetShipmentService;
use InPost\Shipment\Service\Builder\ShipmentRequestBuilder;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment as OrderShipment;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Sales\Model\Order\ShipmentRepository;
use InPost\Shipment\Service\Management\ShipmentManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ShipmentManagerTest extends TestCase
{
    /**
     * @var ShipmentRequestBuilder|MockObject
     */
    private $builderMock;

    /**
     * @var CreateShipmentService|MockObjectz
     */
    private $createShipmentServiceMock;

    /**
     * @var TrackFactory|MockObject
     */
    private $trackFactoryMock;

    /**
     * @var ShipmentRepository|MockObject
     */
    private $shipmentRepositoryMock;

    /**
     * @var ConfigProvider|MockObject
     */
    private $configProviderMock;

    /**
     * @var GetShipmentService|MockObject
     */
    private $getShipmentServiceMock;

    /**
     * @var ShipmentManager
     */
    private $shipmentManager;

    protected function setUp(): void
    {
        $this->builderMock = $this->createMock(ShipmentRequestBuilder::class);
        $this->createShipmentServiceMock = $this->createMock(CreateShipmentService::class);
        $this->trackFactoryMock = $this->createMock(TrackFactory::class);
        $this->shipmentRepositoryMock = $this->createMock(ShipmentRepository::class);
        $this->configProviderMock = $this->createMock(ConfigProvider::class);
        $this->getShipmentServiceMock = $this->createMock(GetShipmentService::class);

        $this->shipmentManager = new ShipmentManager(
            $this->builderMock,
            $this->createShipmentServiceMock,
            $this->getShipmentServiceMock,
            $this->trackFactoryMock,
            $this->shipmentRepositoryMock,
            $this->configProviderMock
        );
    }

    public function testCreateShipment()
    {
        $shipmentMock = $this->createMock(OrderShipment::class);
        $orderMock = $this->createMock(Order::class);
        $shipmentMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $packageOption = 'package_option';

        $this->builderMock->expects($this->once())
            ->method('setOrder')
            ->with($orderMock)
            ->willReturnSelf();

        $this->configProviderMock->expects($this->once())
            ->method('getCompanyName')
            ->willReturn('company_name');
        $this->configProviderMock->expects($this->once())
            ->method('getEmail')
            ->willReturn('email');
        $this->configProviderMock->expects($this->once())
            ->method('getMobilePhoneNumber')
            ->willReturn('mobile_phone_number');

    }
}