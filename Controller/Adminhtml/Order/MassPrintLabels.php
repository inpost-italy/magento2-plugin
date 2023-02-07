<?php

namespace InPost\Shipment\Controller\Adminhtml\Order;

use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Service\Api\GetLabelService;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\OrderRepositoryInterface;

class MassPrintLabels extends \Magento\Backend\App\Action
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
            $orderIds = $this->getRequest()->getParam('selected');
            $resultRedirect = $this->resultRedirectFactory->create();
            $files = [];

            foreach ($orderIds as $orderId) {
                $order = $this->orderRepository->get($orderId);
                if ($order->getShippingMethod() == implode('_', [Inpost::CARRIER_CODE, Inpost::ALLOWED_METHODS])) {
                    $currentFiles = $this->labelService->getLabels($order->getShipmentsCollection());
                    $files = array_merge($currentFiles, $files);
                }
            }

            if (!count($files)) {
                $message = __('No InPost Labels was found in selected orders.');
                throw new \Exception($message->__toString());
            }

            return $this->labelService->downloadArchive($files);

        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $resultRedirect->setPath('sales/order');
        }
    }
}
