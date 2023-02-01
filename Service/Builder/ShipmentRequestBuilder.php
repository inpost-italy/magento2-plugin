<?php

namespace InPost\Shipment\Service\Builder;

use Magento\Framework\DataObjectFactory;

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

    public function setSender(array $data)
    {
        if (!$this->data->getData('sender')) {
            $this->data->setData('sender', $this->objectFactory->create());
        }
        $sender = $this->data->getData('sender');
        $sender->addData($data);
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