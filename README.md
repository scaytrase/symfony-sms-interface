
# symfony-sms-interface
[![Latest Stable Version](https://poser.pugx.org/scaytrase/symfony-sms-interface/v/stable.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface) [![Total Downloads](https://poser.pugx.org/scaytrase/symfony-sms-interface/downloads.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface) [![Latest Unstable Version](https://poser.pugx.org/scaytrase/symfony-sms-interface/v/unstable.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface) [![License](https://poser.pugx.org/scaytrase/symfony-sms-interface/license.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface)

[![Monthly Downloads](https://poser.pugx.org/scaytrase/symfony-sms-interface/d/monthly.png)](https://packagist.org/packages/scaytrase/symfony-sms-interface)
[![Daily Downloads](https://poser.pugx.org/scaytrase/symfony-sms-interface/d/daily.png)](https://packagist.org/packages/scaytrase/symfony-sms-interface)

This Symfony2 bundle provides basic service for sending short messages. This bundle does not provide you any finished implementation for communicating the SMS gateway. To use it you have use some transport implementation or implemenent a transport on your own. See [usage section](#Usage) for known implementations

## Features

### Current features

- [x] Service and configurable transports
- [x] Ability to disable delivery via config for testing purposes
- [x] Ability to force redirect messages for testing purposes
- [x] Profiling via Sf2 Toolbar
 
### Planned features

- [ ] Bulk sending
- [ ] Spooling and delayed sending

## Installation

This bunde could be installed via Composer

```
composer require scaytrase/symfony-sms-delivery-bundle:~1.0
```

### app/AppKernel.php

update your kernel bundle requirements as follows:

```php 
$bundles = array(
    \\....
    new ScayTrase\SmsDeliveryBundle\SmsDeliveryBundle(),
    \\....
    );
```

## Configuration

Below is the configuration example and their default values

```yaml
sms_delivery:
    transport: sms_delivery.dummy_sender # @id of the transport service 
    disable_delivery: false # disables actual delivery making every send return successful result. use profiler to get message details
    delivery_recipient: null # when not null - sends every message to the specified recipient, ignoring actual recipient of the message.
```

## Usage

To use this interface you must create a message class implementing  ``ShortMessageInterface`` and implement a transport that delivers your message to end point sms gateway. You can refer my [WebSMS](https://github.com/scaytrase/symfony-websms-bundle) gateway implementation as a reference.
 
 
### Example

```php 
public function sendSmsAction(){
  $message = new YourMessage('5552368','Help!')
  $result = $this->get('sms_delivery.sender')->send($message);
  return new Response('Delivery '. $result ? 'successful' ; 'failed');
}
```
