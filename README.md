## Glew Magento 2 Service

Glew provides Ecommerce Reporting with Actionable Insights.  Use historical data to uncover your most profitable merchandise, channels, and customer segments.

## Installation

#### Update composer.json
To install, you'll need to be sure that your root `composer.json` file contains a reference to the Glew repository.  To do so, add the following to `composer.json`:

```json
    "repositories": [
        {
            "type": "vcs",                                                                                                              
            "url": "https://github.com/glewio/glew-magento2-service.git"
        }
    ]
```

The above can also be added using the Composer command line with the command: 

    composer config repositories.magento composer http://repo.magento.com/
    
Next, add the required package your root `composer.json` file:

```json
    "require": {
        "glewio/glew-magento2-service": "1.1.0"
    }
```

You can also add this using the Composer command line with the command:

    composer require glewio/glew-magento2-service:1.1.0

#### Run Update
From the command line, run the composer update with the command:

    composer update

#### Run setup:upgrade
From the command line, run setup:upgrade with the command:

    magento setup:upgrade

#### Run di:compile
From the command line, run di:compile with the command:

    magento setup:di:compile
    

## Uninstall
From the command line, run the following command:

    magento module:uninstall Glew_Service
