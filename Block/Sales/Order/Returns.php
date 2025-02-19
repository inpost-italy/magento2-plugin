<?php
namespace InPost\Shipment\Block\Sales\Order;

use InPost\Shipment\Config\ConfigProvider;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Block\Items\AbstractItems;
use Magento\Sales\Model\Order;

/**
 * Sales order view block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Returns extends AbstractItems
{
    /**
     * @var string
     */
    protected $_template = 'InPost_Shipment::order/returns.phtml';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected Registry $coreRegistry;

    /**
     * @var HttpContext
     */
    protected HttpContext $httpContext;

    /**
     * @var ConfigProvider
     */
    protected ConfigProvider $config;

    /**
     * @var bool
     */
    protected $_isScopePrivate;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param HttpContext $httpContext
     * @param ConfigProvider $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        HttpContext $httpContext,
        ConfigProvider $config,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        $this->coreRegistry = $registry;
        $this->config = $config;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve current order model instance
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            return $this->getUrl('*/*/history');
        }
        return $this->getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return Phrase
     */
    public function getBackTitle()
    {
        if ($this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            return __('Back to My Orders');
        }
        return __('View Another Order');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getInvoiceUrl($order)
    {
        return $this->getUrl('*/*/invoice', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getShipmentUrl($order)
    {
        return $this->getUrl('*/*/shipment', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', ['order_id' => $order->getId()]);
    }

    public function getReturnUrl()
    {
        return $this->config->getReturnUrl();
    }
}
