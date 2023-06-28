# InPost Shipment Magento 2 Module
This Magento 2 module adds a new shipping method to your online store called "InPost Shipment". With this module, customers can select this shipping method during checkout, and the order will be shipped using the Italian InPost Shipping Service.

## Installation via compose
```
composer require inpost-italy/magento2-plugin
```

## Manual installation
To install this module, simply download the source code and copy the `InPost` directory to the `app/code` directory of your Magento installation. 

## Process module installation
```bin
bin/magento module:enable InPost_Shipment
bin/magento setup:upgrade
bin/magento setup:di:compile
```

## Configuration
After installing the module, you will need to configure it in the Magento admin panel. Go to `Stores > Configuration > Sales > Delivery Methods` and select `InPost Shipment` from the list of available shipping methods.

Here, you can configure the shipping method's title, description, and price. You will also need to enter your InPost API credentials to enable communication between your store and the InPost shipping service.

## Usage
Once the module is installed and configured, customers can select the InPost shipping method during checkout. The module will then communicate with the InPost shipping service to generate a shipping label and tracking information for the order.

## License
This module is released under the MIT License. Feel free to use and modify it as needed for your Magento 2 store.
