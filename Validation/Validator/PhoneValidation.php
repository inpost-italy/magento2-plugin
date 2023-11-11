<?php

namespace InPost\Shipment\Validation\Validator;

use InPost\Shipment\Validation\AddressRateValidator;
use InPost\Shipment\Validation\ValidationException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\QuoteRepository;

class PhoneValidation implements AddressRateValidator
{
    private QuoteRepository $quoteRepository;

    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    public function validate(RateRequest $rateRequest): void
    {
        // Temporary disable phone validation
        return;
        $items = $rateRequest->getAllItems();
        $item = array_pop($items);

        $quote = $this->quoteRepository->get($item->getQuoteId());
        $phone = $quote->getShippingAddress()->getTelephone();

        if (! $this->validateItalianPhoneNumber($phone)) {
            throw new ValidationException('Phone is not correct');
        }
    }

    function validateItalianPhoneNumber($phoneNumber) : bool
    {
        // Remove any non-digit characters
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // Check if the number starts with the country code '+39'
        if (strpos($phoneNumber, '+39') !== 0) {
            return false;
        }

        // Check if the remaining part of the number consists of 10 digits
        $digits = substr($phoneNumber, 3); // Remove the '+39' part
        if (strlen($digits) !== 10 || !ctype_digit($digits)) {
            return false;
        }

        return true;
    }
}
