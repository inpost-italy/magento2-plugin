<?php

namespace InPost\Shipment\Service\Builder;

use InPost\Shipment\Api\Data\CreateShipmentRequestFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Sales\Model\Order;

class ShipmentRequestBuilder
{
    private $data;

    /** @var CreateShipmentRequestFactory */
    private $createShipmentRequestFactory;

    /**
     * @var DataObjectFactory
     */
    private $objectFactory;

    /**
     * @param CreateShipmentRequestFactory $createShipmentRequest
     * @param DataObjectFactory $objectFactory
     */
    public function __construct(
        CreateShipmentRequestFactory $createShipmentRequest,
        DataObjectFactory $objectFactory
    ) {
        $this->createShipmentRequestFactory = $createShipmentRequest;
        $this->data = $objectFactory->create();
        $this->objectFactory = $objectFactory;
    }

    public function setReceiver(array $data): void
    {
        $this->data->setReceiver($data);
    }

    public function setOrder(Order $order): void
    {
        $address = $order->getShippingAddress();
        $this->setReceiver([
            'name' => "{$order->getCustomerFirstname()} {$order->getCustomerLastname()}",
            'first_name' => $order->getCustomerFirstname(),
            'last_name' => $order->getCustomerLastname(),
            'email' => $order->getCustomerEmail(),
            'phone' => $address->getTelephone(),
            'address' => [
                'city' => $address->getCity(),
                'post_code' => $address->getPostcode(),
                'street' => $address->getStreet()[0],
                // @TODO FIX
                'building_number' => '1',
            ],
        ]);

        $this->data->setReference($order->getIncrementId());
    }

    public function setTargetPointID(string $point): void
    {
        $this->addCustomAttribute('target_point', $point);
    }

    public function setSender(array $data): void
    {
        $this->data->setSender($data);
    }

    public function setParcels(array $data): void
    {
        $this->data->setParcels($data);
    }

    public function setService($service): void
    {
        $this->data->setService($service);
    }

    public function setComment($comment): void
    {
        $this->data->setComment($comment);
    }

    public function addCustomAttribute($attribute, $value): void
    {
        $data = $this->data->getCustomAttributes() ?? [];
        $data[$attribute] = $value;
        $this->data->setCustomAttributes($data);
    }

    public function build()
    {
        return $this->createShipmentRequestFactory->create(
            ['data' => $this->data->toArray()]
        );
    }
}
