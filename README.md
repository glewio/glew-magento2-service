## Glew Magento 2 Service

Glew provides Ecommerce Reporting with Actionable Insights.  Use historical data to uncover your most profitable merchandise, channels, and customer segments.

#### Install via Composer
To install, you'll need to be sure that your root `composer.json` file contains a reference to the Glew repository.  To do so, run the following command:

    composer config repositories.glewio/glew-magento2-service vcs https://github.com/glewio/glew-magento2-service.git

Next, require the package:

    composer require glewio/glew-magento2-service

#### Run setup:upgrade
From the command line, run setup:upgrade with the command:

    magento setup:upgrade

#### Run cache:flush
From the command line, run cache:flush with the command:

    magento cache:flush
