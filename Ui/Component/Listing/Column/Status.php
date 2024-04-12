<?php
namespace InPost\Shipment\Ui\Component\Listing\Column;

use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Service\Api\TrackingService;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Track;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * @var TrackingService
     */
    protected TrackingService $trackingService;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param TrackingService $trackingService
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        TrackingService $trackingService,
        array $components = [],
        array $data = []
    )
    {
        $this->orderRepository = $orderRepository;
        $this->trackingService = $trackingService;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {

        if (isset($dataSource['data']['items']))
        {
            foreach ($dataSource['data']['items'] as & $item)
            {
                $item[$this->getData('name')] = '';

                /**
                 * @var Order $order
                 */
                $order = $this->orderRepository->get($item["entity_id"]);
                if(strpos($order->getShippingMethod(), Inpost::CARRIER_CODE) !== false)
                {
                    foreach($order->getShipmentsCollection() as $shipment)
                    {
                        /**
                         * @var Shipment $shipment
                         */
                        if(count($shipment->getTracks() ) > 0)
                        {
                            foreach ($shipment->getTracks() as $track)
                            {
                                /**
                                 * @var Track $track
                                 */
                                $trackingNumber = $track->getTrackNumber();
                                try {
                                    $trackingStatus = $this->trackingService->getTracking($trackingNumber);
                                    if($trackingStatus->getStatus())
                                    {
                                        $options = [
                                            'content' => __($trackingStatus->getStatus()['title']),
                                            'tracking_number' => $trackingNumber,
                                            'url' => $trackingStatus->getUrl(),
                                            'description' => __($trackingStatus->getStatus()['description'])
                                        ];
                                        $item[$this->getData('name')] = json_encode($options);
                                    }
                                }
                                catch (\Exception $e) {}
                            }
                        }
                    }
                }
            }
        }

        return $dataSource;
    }
}
