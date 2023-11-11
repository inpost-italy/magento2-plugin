<?php

namespace InPost\Shipment\Validation;

use Magento\Quote\Model\Quote\Address\RateRequest;

interface AddressRateValidator
{
    /**
     * @throws ValidationException
     * @return void
     */
    public function validate(RateRequest $rateRequest) : void;
}
