<?php

namespace InPost\Shipment\Console\Command;

use InPost\Shipment\Service\Management\OrderManager;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CloseOrders extends Command
{
    /**
     * @var State
     */
    private State $state;

    /**
     * @var OrderManager
     */
    private OrderManager $orderManager;

    /**
     * @param State $state
     * @param OrderManager $orderManager
     * @param string|null $name
     */
    public function __construct
    (
        State $state,
        OrderManager $orderManager,
        ?string $name = null
    )
    {
        $this->state = $state;
        $this->orderManager = $orderManager;
        parent::__construct($name);
    }

    /**
     * Initialization of the command.
     */
    protected function configure()
    {
        $this->setName('inpost:order:close');
        $this->setDescription('Close Orders');
        parent::configure();
    }

    /**
     * CLI command description.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->state->setAreaCode('adminhtml');
        $this->orderManager->closeOrders();
        return 0;
    }
}
