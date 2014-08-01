
# symfony-sms-interface
[![Latest Stable Version](https://poser.pugx.org/scaytrase/symfony-sms-interface/v/stable.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface) [![Total Downloads](https://poser.pugx.org/scaytrase/symfony-sms-interface/downloads.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface) [![Latest Unstable Version](https://poser.pugx.org/scaytrase/symfony-sms-interface/v/unstable.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface) [![License](https://poser.pugx.org/scaytrase/symfony-sms-interface/license.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface)

[![Monthly Downloads](https://poser.pugx.org/scaytrase/symfony-sms-interface/d/monthly.png)](https://packagist.org/packages/scaytrase/symfony-sms-interface)
[![Daily Downloads](https://poser.pugx.org/scaytrase/symfony-sms-interface/d/daily.png)](https://packagist.org/packages/scaytrase/symfony-sms-interface)

This Symfony2 bundle provides basic interface for sending short messages as a service

## Current features

- Extensible inteface and basic service class
- Ability to disable delivery via config for testing purposes
- Ability to force redirect messages for testing purposes


## Installation


### Composer

```
require: "scaytrase/symfony-sms-interface": "~1.2.0"
```

### app/AppKernel.php

update your kernel bundle requirements as follows:
```
$bundles = array(
    ....
    new ScayTrase\Utils\SMSDeliveryBundle\SMSDeliveryBundle(),
    ....
    );
```

