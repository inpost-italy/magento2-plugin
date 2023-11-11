<?php

namespace InPost\Shipment\Validation;

use Magento\Quote\Model\Quote\Address\RateRequest;

class ValidatorPool implements AddressRateValidator
{
    /**
     * @var AddressRateValidator[]
     */
    private array $validators;

    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    /**
     * @param RateRequest $request
     * @return void
     * @throws ValidationException
     */
    public function validate(RateRequest $rateRequest) : void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($rateRequest);
        }
    }
}
