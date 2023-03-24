<?php

namespace InPost\Shipment\Service\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Laminas\Http\Request;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private $authToken;

    /**
     * @param $token
     * @return void
     */
    public function setAuthToken($token): void
    {
        $this->authToken = $token;
    }

    /**
     * @param $path
     * @param $params
     *
     * @return ResponseInterface
     * @throws HttpClientException
     */
    public function get($path, $params = []): ResponseInterface
    {
        $client = new GuzzleClient;
        $endpoint = $path . '?' . http_build_query($params);

        try {
            $response = $client->get($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$this->authToken}"
                ]
            ]);
        } catch (GuzzleException $e) {
            throw new HttpClientException($e->getMessage());
        }

        if ($response->getStatusCode() != 200) {
            throw new HttpClientException($response->getBody());
        }

        return $response;
    }

    public function post($path, $body): ResponseInterface
    {
        $client = new GuzzleClient;

        try {
            $response = $client->post($path, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$this->authToken}"
                ],
                'body' => $body
            ]);
        } catch (GuzzleException $e) {
            throw new HttpClientException($e->getMessage());
        }

        if ($response->getStatusCode() > 210) {
            throw new HttpClientException($response->getBody());
        }

        return $response;
    }
}
