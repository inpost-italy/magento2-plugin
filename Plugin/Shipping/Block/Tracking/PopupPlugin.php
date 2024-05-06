<?php
namespace InPost\Shipment\Plugin\Shipping\Block\Tracking;

use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Config\ConfigProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\OrderRepository;
use Magento\Shipping\Block\Tracking\Popup;
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
     * @var OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * @var ShipmentRepository
     */
    protected ShipmentRepository $shipmentRepository;

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
     * @param OrderRepository $orderRepository
     * @param ShipmentRepository $shipmentRepository
     * @param SecureHtmlRenderer $secureRenderer
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        InfoFactory $shippingInfoFactory,
        RequestInterface $request,
        TrackFactory $trackFactory,
        OrderRepository $orderRepository,
        ShipmentRepository $shipmentRepository,
        SecureHtmlRenderer $secureRenderer,
        ConfigProvider $configProvider
    )
    {
        $this->shippingInfoFactory = $shippingInfoFactory;
        $this->request = $request;
        $this->trackFactory = $trackFactory;
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->secureRenderer = $secureRenderer;
        $this->configProvider = $configProvider;
    }

    public function aroundToHtml(Popup $subject, $proceed)
    {
        $shippingInfoModel = $this->shippingInfoFactory->create()->loadByHash($this->request->getParam('hash'));
        if($shippingInfoModel->getOrderId())
        {
            try {
                $order = $this->orderRepository->get($shippingInfoModel->getOrderId());
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
            catch (\Exception $e) {}
        }
        if($shippingInfoModel->getShipId())
        {
            try {
                $shipment = $this->shipmentRepository->get($shippingInfoModel->getShipId());
                foreach ($shipment->getTracks() as $track)
                {
                    return $this->closePupupAndLink($track->getTrackNumber());
                }
            }
            catch (\Exception $e) {}
        }
        if($shippingInfoModel->getTrackId())
        {
            try {
                /** @var Track $track */
                $track = $this->trackFactory->create()->load($shippingInfoModel->getTrackId());
                if($track->getCarrierCode() && (strpos($track->getCarrierCode(), Inpost::CARRIER_CODE) !== false))
                {
                    return $this->closePupupAndLink($track->getTrackNumber());
                }
            }
            catch (\Exception $e) {}
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
