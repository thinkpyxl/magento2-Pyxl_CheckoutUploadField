# Pyxl_CheckoutUploadField
This module adds a field to the checkout page for guests to upload a purchase order form.

## Getting Started
To install into your existing Magento site run the following two commands.

    composer config repositories.pyxl-checkoutuploadfield git https://bitbucket.org/pyxlinc/pyxl_checkoutuploadfield.git
    composer require pyxl/module-checkoutuploadfield:^1.0.0
    bin/magento module:enable Pyxl_CheckoutUploadField
    bin/magento setup:upgrade
    bin/magento cache:clean

There are no settings for this module.

## Authors
* Justin Rhyne
* Joel Rainwater
