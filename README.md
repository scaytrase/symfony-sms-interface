
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

Installation is available via Composer

### composer.json


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

## Configuration

Basic interface supports two optional parameters:

```
sms_delivery:
    disable_delivery: true # disables actual delivery making every send return successful result. use profiler to get message details
    delivery_recipient: null # when not null - sends every message to the specified recipient, ignoring actual recipient of the message.
```

## Usage

To use this interface you must create a message class implementing  ``ShortMessageInterface`` and extend abstract MessageDeliveryService with your own delivery api service. As soon as you a ready to go, you must specify sender class via ``sms_delivery.class`` parameter.
 
 Refer [DummyMessageDeliveryService](src/ScayTrase/Utils/SMSDeliveryBundle/Service/DummySender/DummyMessageDeliveryService.php) as an example. It is also set as a default delivery service for quick start usage.


### Example

```
public function sendSmsAction(){
  $message = new YourMessage('5552368','Help!')
  $sender = $this->get('sms_delivery.sender');
  $result = $sender->send($message);
  return new Response('Delivery '. $result ? 'successful' ; 'failed');
}
```