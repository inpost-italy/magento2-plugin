<?php

namespace InPost\Shipment\Block\Sales\Order;

class History extends \Magento\Sales\Block\Order\History
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \InPost\Shipment\Config\ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig, $data);
    }

    public function getReturnUrl()
    {
        return $this->configProvider->getReturnUrl();
    }
}