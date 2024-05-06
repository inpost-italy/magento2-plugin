<?php
namespace InPost\Shipment\Block\Sales\Order\Returns;

use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Config\ConfigProvider;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Html\Link\Current;
use Magento\Framework\View\Element\Template\Context;

/**
 * Sales order link
 *
 * @api
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 * @since 100.0.2
 */
class Link extends Current
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ConfigProvider
     */
    protected ConfigProvider $config;

    /**
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param Registry $registry
     * @param ConfigProvider $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        Registry $registry,
        ConfigProvider $config,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->config = $config;
        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->getPath(), ['order_id' => $this->getOrder()->getId()]);
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->hasKey()
            && method_exists($this->getOrder(), 'has' . $this->getKey())
            && !$this->getOrder()->{'has' . $this->getKey()}()
        ) {
            return '';
        }
        if(count($this->getOrder()->getShipmentsCollection()) == 0)
        {
            return '';
        }
        if(!$this->config->canReturnAll())
        {
            if(strpos($this->getOrder()->getShippingMethod(), Inpost::CARRIER_CODE) === false)
            {
                return '';
            }
        }
        if(!$this->config->getReturnUrl())
        {
            return '';
        }
        return parent::_toHtml();
    }
}
