<?php
declare(strict_types=1);

namespace InPost\Shipment\Setup;

use Exception;
use InPost\Shipment\Config\ConfigProvider;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface
{

    /**
     * Custom Order-Status code
     */
    const ORDER_STATUS_SHIPPING = 'shipping';

    /**
     * Custom Order-Status label
     */
    const ORDER_STATUS_LABEL = 'Shipping';

    /**
     * Status Factory
     *
     * @var StatusFactory
     */
    protected $statusFactory;

    /**
     * Status Resource Factory
     *
     * @var StatusResourceFactory
     */
    protected $statusResourceFactory;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /**
     * InstallData constructor
     *
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }
    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     *
     * @throws Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->addNewOrderStateAndStatus();
        $this->addAllowInpostDeliveryAttribute($setup);
    }

    /**
     * Create new custom order status and assign it to the new custom order state
     *
     * @return void
     *
     * @throws Exception
     */
    protected function addNewOrderStateAndStatus()
    {
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
    }

    private function addAllowInpostDeliveryAttribute($setup)
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
