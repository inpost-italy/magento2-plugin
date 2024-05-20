<?php declare(strict_types=1);

namespace InPost\Shipment\Service\Builder\DataProcessor;

use InPost\Shipment\Config\Source\FlowSources;
use InPost\Shipment\Service\Builder\ShipmentRequestBuilder;

class ServiceProcessor implements \InPost\Shipment\Service\Builder\DataProcessor\ShipmentDataProcessorInterface
{
    private \InPost\Shipment\Config\ConfigProvider $configProvider;

    public function __construct(\InPost\Shipment\Config\ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param ShipmentRequestBuilder $builder
     * @return void
     */
    public function process(ShipmentRequestBuilder $builder)
    {
        $builder->setService('inpost_locker_standard');

        // L2L set sending_method to parcel_locker
        if ($this->configProvider->getService() == FlowSources::FLOW_SERVICE_TYPE_LOCKER) {
            $builder->addCustomAttribute('sending_method', 'parcel_locker');
            return;
        }

        // H2L set sender information
        $builder->setSender([
            'company_name' => $this->configProvider->getCompanyName(),
            'email' => $this->configProvider->getEmail(),
            'phone' => $this->configProvider->getMobilePhoneNumber(),
            'address' => $this->configProvider->getSenderAddress()
        ]);
        $comment = 'Magento-' . ($this->configProvider->isDebugModeEnabled() ? 'staging' : 'production');
        $builder->setComment($comment);
    }
}
