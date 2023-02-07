<?php

namespace InPost\Shipment\Plugin\Adminhtml\Order\Shipping;

use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Api\GetLabelService;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Shipment\AbstractShipment\Pdfshipments;

class PdfShipmentsMassAction
{
    private $labelService;
    private $configProvider;
    private $orderRepository;

    /**
     * @param GetLabelService $labelService
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        GetLabelService $labelService,
        ConfigProvider $configProvider,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->labelService = $labelService;
        $this->configProvider = $configProvider;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Pdfshipments $subject
     * @param callable $proceed
     * @param AbstractCollection $collection
     * @return ResponseInterface
     * @throws \InPost\Shipment\Service\Http\HttpClientException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function aroundMassAction(
        Pdfshipments $subject,
        callable $proceed,
        AbstractCollection $collection
    ): ResponseInterface {
        if ($this->configProvider->isActive()) {
            $orderId = $subject->getRequest()->getParam('order_id');
            $order = $this->orderRepository->get($orderId);
            if ($order->getShippingMethod() == implode('_', [Inpost::CARRIER_CODE, Inpost::ALLOWED_METHODS])) {
                $files = $this->labelService->getLabels($collection);
                return $this->labelService->downloadArchive($files);
            }
        }

        return $proceed($collection);
    }
}
