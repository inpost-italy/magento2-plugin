<?php
declare(strict_types=1);

namespace InPost\Shipment\Setup;

use InPost\Shipment\Config\ConfigProvider;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(Category::ENTITY, ConfigProvider::ALLOW_INPOST_DELIVERY_CATEGORY_ATTRIBUTE, [
            'type'     => 'int',
            'label'    => 'Eligible for Inpost Delivery',
            'input'    => 'boolean',
            'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'visible'  => true,
            'default'  => '1',
            'required' => false,
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'group'    => 'Display Settings',
        ]);
    }
}
