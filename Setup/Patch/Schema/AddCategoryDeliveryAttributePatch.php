<?php

namespace InPost\Shipment\Setup\Patch\Schema;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Sales\Model\Order\StatusFactory;

class AddCategoryDeliveryAttributePatch implements SchemaPatchInterface
{
    private $moduleDataSetup;

    /**
     * Custom Order-Status code
     */
    const ORDER_STATUS_SHIPPING = 'shipping';

    /**
     * Custom Order-Status label
     */
    const ORDER_STATUS_LABEL = 'Shipping';

    private StatusFactory $statusFactory;

    private StatusResourceFactory $statusResourceFactory;

    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StatusFactory $statusFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->statusFactory = $statusFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }


    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $statuses = [
            [
                'status' => self::ORDER_STATUS_SHIPPING,
                'label' => self::ORDER_STATUS_LABEL,
            ]
        ];

        foreach ($statuses as $status) {
            $this->moduleDataSetup->getConnection()->insertForce(
                $this->moduleDataSetup->getTable('sales_order_status'),
                $status
            );

            $this->moduleDataSetup->getConnection()->insertForce(
                $this->moduleDataSetup->getTable('sales_order_status_state'),
                [
                    'status' => $status['status'],
                    'state' => 'new',
                    'is_default' => 0,
                ]
            );
        }
        $this->moduleDataSetup->endSetup();
    }
}