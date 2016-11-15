## Glew Magento 2 Service

Glew provides Ecommerce Reporting with Actionable Insights.  Use historical data to uncover your most profitable merchandise, channels, and customer segments.

## Installation

Before installing, it is recommended that you disable your cache in System -> Cache Mangement.

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

    composer config repositories.glew vcs https://github.com/glewio/glew-magento2-service.git
    
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
    
After you have completed these steps, you should clear your cache at System -> Cache Management and then click Flush Cache.  If you have disabled your cache, it can be re-enabled.  Depending on your environment, you may need to clear the following directoris when adding a new module:  var/di, var/generation.  From the command line, you can run the following command to clear your directories:
    
    rm -rf **<your Magento install dir>**/var/di/* **<your Magento install dir>**/var/generation/*

## Uninstall
From the command line, run the following command:

    magento module:uninstall Glew_Service
