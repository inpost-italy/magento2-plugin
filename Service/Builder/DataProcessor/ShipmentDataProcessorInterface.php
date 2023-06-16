<?php

namespace InPost\Shipment\Service\Builder\DataProcessor;

use InPost\Shipment\Service\Builder\ShipmentRequestBuilder;

interface ShipmentDataProcessorInterface
{
    /**
     * @param ShipmentRequestBuilder $builder
     * @return mixed
     */
    public function process(ShipmentRequestBuilder $builder);
}