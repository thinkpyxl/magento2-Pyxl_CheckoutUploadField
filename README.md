# Pyxl_CheckoutUploadField
This module adds a field to the checkout page for guests to upload a purchase order form.

## Getting Started
To install into your existing Magento site run the following two commands.

    composer config repositories.pyxl-checkoutuploadfield git git@bitbucket.org:pyxlinc/module-shippingnotes.git
    composer require pyxl/module-checkoutuploadfield:^0.0.1
    bin/magento module:enable Pyxl_CheckoutUploadField
    bin/magento setup:upgrade
    bin/magento cache:clean

There are no settings for this module.

## Authors
* Justin Rhyne
* Joel Rainwater