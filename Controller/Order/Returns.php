<?php
namespace InPost\Shipment\Controller\Order;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Sales\Controller\AbstractController\View;
use Magento\Sales\Controller\OrderInterface;

class Returns extends View implements OrderInterface, HttpGetActionInterface
{
}
