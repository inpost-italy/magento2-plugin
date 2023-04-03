<?php

namespace InPost\Shipment\Setup\Setup\Patch\Schema;

use InPost\Shipment\Config\ConfigProvider;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;

class AddCategoryDeliveryAttribute implements SchemaPatchInterface
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

    private EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
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

        /** @var StatusResource $statusResource */
        $statusResource = $this->statusResourceFactory->create();
        /** @var Status $status */
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => self::ORDER_STATUS_SHIPPING,
            'label' => self::ORDER_STATUS_LABEL,
        ]);
        try {
            $statusResource->save($status);
        } catch (AlreadyExistsException $exception) {
            return;
        }
        $status->assignState(self::ORDER_STATUS_SHIPPING, true, true);

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(Category::ENTITY, ConfigProvider::ALLOW_INPOST_DELIVERY_CATEGORY_ATTRIBUTE, [
            'type'     => 'int',
            'label'    => 'Eligible for Inpost Delivery 2',
            'input'    => 'boolean',
            'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'visible'  => true,
            'default'  => '1',
            'required' => false,
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'group'    => 'Display Settings',
        ]);

        $this->moduleDataSetup->endSetup();
    }
}