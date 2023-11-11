<?php

namespace InPost\Shipment\Service\Quote;

use InPost\Shipment\Api\Data\PointsServiceRequestFactory;
use InPost\Shipment\Service\Api\ApiServiceProvider;
use Magento\Quote\Model\QuoteRepository;

class PointsExtractor
{
    private QuoteRepository $quoteRepository;
    private ApiServiceProvider $apiServiceProvider;
    private PointsServiceRequestFactory $pointsServiceRequestFactory;

    public function __construct(
        QuoteRepository $quoteRepository,
        ApiServiceProvider $apiServiceProvider,
        PointsServiceRequestFactory $pointsServiceRequestFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->apiServiceProvider = $apiServiceProvider;
        $this->pointsServiceRequestFactory = $pointsServiceRequestFactory;
    }

    public function getInpostPoint($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $inpostPoint = $quote->getShippingAddress()->getInpostPointId();
        if (! $inpostPoint) {
            return null;
        }
        $points = $this->callPointsService($inpostPoint);

        return !$points->isEmpty() ? $points->getFirstItem()->getPointInfo() : null;

    }

    private function callPointsService($pointId)
    {
        $apiService = $this->apiServiceProvider->getPointsApiService();
        $request = $this->createRequest($pointId);

        return $apiService->getPoints($request);
    }

    public function createRequest($pointId)
    {
        $apiPointsRequest = $this->pointsServiceRequestFactory->create();
        $apiPointsRequest->setName($pointId);

        return $apiPointsRequest;
    }
}
