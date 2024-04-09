<?php
namespace InPost\Shipment\Plugin\Shipping\Block\Tracking;

use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Config\ConfigProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Sales\Model\OrderFactory;
use Magento\Shipping\Block\Tracking\Popup;
use Magento\Shipping\Model\Info;
use Magento\Shipping\Model\InfoFactory;
use Magento\Shipping\Model\Order\Track;
use Magento\Shipping\Model\Order\TrackFactory;

class PopupPlugin
{
    /**
     * @var InfoFactory
     */
    protected InfoFactory $shippingInfoFactory;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var TrackFactory
     */
    protected TrackFactory $trackFactory;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var SecureHtmlRenderer
     */
    protected SecureHtmlRenderer $secureRenderer;

    /**
     * @var ConfigProvider
     */
    protected ConfigProvider $configProvider;

    /**
     * @param InfoFactory $shippingInfoFactory
     * @param RequestInterface $request
     * @param TrackFactory $trackFactory
     * @param OrderFactory $orderFactory
     * @param SecureHtmlRenderer $secureRenderer
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        InfoFactory $shippingInfoFactory,
        RequestInterface $request,
        TrackFactory $trackFactory,
        OrderFactory $orderFactory,
        SecureHtmlRenderer $secureRenderer,
        ConfigProvider $configProvider
    )
    {
        $this->shippingInfoFactory = $shippingInfoFactory;
        $this->request = $request;
        $this->trackFactory = $trackFactory;
        $this->orderFactory = $orderFactory;
        $this->secureRenderer = $secureRenderer;
        $this->configProvider = $configProvider;
    }

    public function aroundToHtml(Popup $subject, $proceed)
    {
        $shippingInfoModel = $this->shippingInfoFactory->create()->loadByHash($this->request->getParam('hash'));
        if($shippingInfoModel->getOrderId())
        {
            $order = $this->orderFactory->create()->load($shippingInfoModel->getOrderId());
            if(strpos($order->getShippingMethod(), Inpost::CARRIER_CODE) !== false)
            {
                foreach ($order->getShipmentsCollection() as $shipment)
                {
                    foreach ($shipment->getTracks() as $track)
                    {
                        return $this->closePupupAndLink($track->getTrackNumber());
                    }

                }

            }
        }
        if($shippingInfoModel->getTrackId())
        {
            /** @var Track $track */
            $track = $this->trackFactory->create()->load($shippingInfoModel->getTrackId());
            if($track->getCarrierCode() && (strpos($track->getCarrierCode(), Inpost::CARRIER_CODE) !== false))
            {
                return $this->closePupupAndLink($track->getTrackNumber());
            }
        }
        return $proceed();
    }

    public function closePupupAndLink($trackNumber)
    {
        $trackingUrl = str_replace('{{tracknumber}}', $trackNumber ,$this->configProvider->getTrackingUrl());
        $scriptString = <<<script

            require([
                'jquery'
            ], function (jQuery) {
                //parent.window.open('$trackingUrl', '_blank');
                //window.close();
                window.location.href = '$trackingUrl';
            });

        script;

        return $this->secureRenderer->renderTag('script', [], $scriptString, false);
    }
}
