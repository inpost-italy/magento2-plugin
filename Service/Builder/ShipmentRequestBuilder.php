<?php

namespace InPost\Shipment\Service\Builder;

use Magento\Framework\DataObjectFactory;
use Magento\Sales\Model\Order;

class ShipmentRequestBuilder
{
    private \InPost\Shipment\Api\Data\CreateShipmentRequestFactory $createShipmentRequestFactory;

    public function __construct(
        \InPost\Shipment\Api\Data\CreateShipmentRequestFactory $createShipmentRequest,
        DataObjectFactory $objectFactory
    ){
        $this->createShipmentRequestFactory = $createShipmentRequest;
        $this->data = $objectFactory->create();
        $this->objectFactory = $objectFactory;
    }

    /**
     * @var DataObjectFactory
     */
    private $objectFactory;

    public function setReceiver(array $data)
    {
       $this->data->setReceiver($data);
    }

    public function setOrder(Order $order)
    {
        $address = $order->getShippingAddress();
        $this->setReceiver([
            'name'          => "{$order->getCustomerFirstname()} {$order->getCustomerLastname()}",
            'first_name'    => $order->getCustomerFirstname(),
            'last_name'     => $order->getCustomerLastname(),
            'email'         => $order->getCustomerEmail(),
            'phone'         => $address->getTelephone(),
            'address'       => [
                'city'      => $address->getCity(),
                'post_code' => $address->getPostcode(),
                'street'    => $address->getStreet()[0],
                // @TODO FIX
                'building_number' => '1',
            ],
        ]);

        $this->setCustomAttributes([
            'target_point' => $address->getInpostPointId()
        ]);
    }


    public function setSender(array $data)
    {
        $this->data->setSender($data);
    }

    public function setParcels(array $data)
    {
        $this->data->setParcels($data);
    }

    public function setService($service)
    {
        $this->data->setService($service);
    }

    public function setCustomAttributes($service)
    {
        $this->data->setCustomAttributes($service);
    }

    public function build()
    {
        return $this->createShipmentRequestFactory->create(['data' => $this->data->toArray()]);
    }
}
