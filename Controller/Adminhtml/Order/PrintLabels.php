<?php

namespace InPost\Shipment\Controller\Adminhtml\Order;

use InPost\Shipment\Service\Api\GetLabelService;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\OrderRepositoryInterface;

class PrintLabels extends \Magento\Backend\App\Action
{
    protected $orderRepository;
    protected $labelService;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        GetLabelService $labelService
    ) {
        $this->orderRepository = $orderRepository;
        $this->labelService = $labelService;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        try {
            $resultRedirect = $this->resultRedirectFactory->create();
            $orderId = $this->getRequest()->getParam('order_id');
            $order = $this->orderRepository->get($orderId);
            $files = $this->labelService->getLabels($order->getShipmentsCollection());
            return $this->labelService->downloadArchive($files);

        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        }
    }
}
