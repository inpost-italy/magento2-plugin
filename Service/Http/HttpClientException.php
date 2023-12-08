<?php

namespace InPost\Shipment\Service\Http;

class HttpClientException extends \Exception
{
    public function getReason() : string
    {
        $data = json_decode($this->message, true);

        // Check if JSON decoding was successful
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return "Invalid JSON";
        }

        // Check if the "message" field exists
        if (isset($data['message'])) {
            return $data['message'] . json_encode($data['details']) ?? '';
        } else {
            return "Reason not found in JSON";
        }
    }
}
