<?php
declare(strict_types=1);

namespace InPost\Shipment\Block\Adminhtml\Order\View\Buttons;

use AdobeStock\Api\Core\Config;
use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Config\ConfigProvider;
use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Shipment;

class LabelsButton extends Container
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /** @var ConfigProvider */
    private $configProvider;

    /**
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param ConfigProvider $configProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderRepository = $orderRepository;
        $this->configProvider = $configProvider;
    }

    /**
     * Check if button should be shown
     *
     * @return bool
     */
    public function canShowButton(): bool
    {
        if ($this->configProvider->isActive()) {
            $orderId = $this->getRequest()->getParam('order_id');
            $order = $this->orderRepository->get($orderId);

            if ($order->getShippingMethod() == implode('_', [Inpost::CARRIER_CODE, Inpost::ALLOWED_METHODS])) {
                $shipments = $order->getShipmentsCollection();

                // TODO: Remove after adding `inpost_shipment_id` to shipping table
                // TODO: and use full validation check
                if (count($shipments)) {
                    return true;
                }

                /** @var ShipmentInterface $shipment */
                foreach ($shipments as $shipment) {
                    $shipment->getTracks();
                    /** @var Shipment $shipment */
                    if (!empty($shipment->getInpostShipmentId())) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getButtonUrl(): string
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('inpost_shipment/order/printLabels', ['order_id' => $orderId]);
    }
}
