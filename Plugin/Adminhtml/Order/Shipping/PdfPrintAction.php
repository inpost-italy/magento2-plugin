<?php

namespace InPost\Shipment\Plugin\Adminhtml\Order\Shipping;

use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Api\GetLabelService;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Shipment\AbstractShipment\PrintAction;

class PdfPrintAction
{
    private $orderRepository;
    private $shipmentRepository;
    private $configProvider;
    private $labelService;

    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface $orderRepository,
        ConfigProvider $configProvider,
        GetLabelService $labelService
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->configProvider = $configProvider;
        $this->labelService = $labelService;
    }

    /**
     * @param PrintAction $subject
     * @param callable $proceed
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \InPost\Shipment\Service\Http\HttpClientException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function aroundExecute(PrintAction $subject, callable $proceed)
    {
        if ($this->configProvider->isActive()) {
            $shipmentId = $subject->getRequest()->getParam('shipment_id');
            $shipment = $this->shipmentRepository->get($shipmentId);
            $orderId = $shipment->getOrderId();
            $order = $this->orderRepository->get($orderId);

            if ($order->getShippingMethod() == implode('_', [Inpost::CARRIER_CODE, Inpost::ALLOWED_METHODS])) {
                $files = $this->labelService->getLabel($shipment);
                return $this->labelService->downloadArchive($files);
            }
        }

        return $proceed();
    }
}
