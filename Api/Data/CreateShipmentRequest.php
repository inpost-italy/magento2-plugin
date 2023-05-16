<?php

namespace InPost\Shipment\Api\Data;

use Magento\Framework\DataObject;

class CreateShipmentRequest extends DataObject
{
    private $receiver;
    private $sender;
    private $parcels;
    private $service;
    private $customAttributes;
    private $reference;
    private $additionalServices;
    private $externalCustomerId;

    /**
     * @return mixed
     */
    public function getExternalCustomerId()
    {
        return $this->externalCustomerId;
    }

    /**
     * @param mixed $externalCustomerId
     */
    public function setExternalCustomerId($externalCustomerId): void
    {
        $this->externalCustomerId = $externalCustomerId;
    }

    /**
     * @return mixed
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param mixed $receiver
     */
    public function setReceiver($receiver): void
    {
        $this->receiver = $receiver;
    }

    /**
     * @return mixed
     */
    public function getParcels()
    {
        return $this->parcels;
    }

    /**
     * @param mixed $parcels
     */
    public function setParcels($parcels): void
    {
        $this->parcels = $parcels;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return mixed
     */
    public function getAdditionalServices()
    {
        return $this->additionalServices;
    }

    /**
     * @param mixed $additionalServices
     */
    public function setAdditionalServices($additionalServices): void
    {
        $this->additionalServices = $additionalServices;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service): void
    {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    /**
     * @param mixed $customAttributes
     */
    public function setCustomAttributes($customAttributes): void
    {
        $this->customAttributes = $customAttributes;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @param mixed $sender
     */
}
