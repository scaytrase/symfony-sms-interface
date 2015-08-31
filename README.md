# scaytrase/symfony-sms-delivery-bundle
[![Latest Stable Version](https://poser.pugx.org/scaytrase/symfony-sms-interface/v/stable.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface)
[![Total Downloads](https://poser.pugx.org/scaytrase/symfony-sms-interface/downloads.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface)
[![Latest Unstable Version](https://poser.pugx.org/scaytrase/symfony-sms-interface/v/unstable.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface)
[![License](https://poser.pugx.org/scaytrase/symfony-sms-interface/license.svg)](https://packagist.org/packages/scaytrase/symfony-sms-interface)

[![Monthly Downloads](https://poser.pugx.org/scaytrase/symfony-sms-interface/d/monthly.png)](https://packagist.org/packages/scaytrase/symfony-sms-interface)
[![Daily Downloads](https://poser.pugx.org/scaytrase/symfony-sms-interface/d/daily.png)](https://packagist.org/packages/scaytrase/symfony-sms-interface)

This Symfony bundle provides basic service for sending short messages. This bundle does not provide you any finished
implementation for communicating the SMS gateway. To use it you have use some transport implementation or
implement a transport on your own. See [usage section](#usage) for known implementations

## Features

### Current features

- [x] Service and configurable transports
- [x] Ability to disable delivery via config for testing purposes
- [x] Ability to force redirect messages for testing purposes
- [x] Profiling via Sf2 Toolbar
- [x] Spooling and delayed sending

### Planned features

- [ ] Bulk sending

## Installation

This bunde could be installed via Composer

```
composer require scaytrase/symfony-sms-delivery-bundle:~2.0
```

### app/AppKernel.php

update your kernel bundle requirements as follows:

```php 
$bundles = array(
    //....
    new ScayTrase\SmsDeliveryBundle\SmsDeliveryBundle(),
    //....
    );
```

## Configuration

Below is the configuration example and their default values

```yaml
sms_delivery:
    spool: sms_delivery.spool.instant
    transport: sms_delivery.dummy_sender # @id of the transport service 
    disable_delivery: false # disable delivery overrides spool with disabled spool
    delivery_recipient: null # delivery recipient overrides recipient when sending
```

## Usage

To use this interface you must create a message class implementing  ``ShortMessageInterface`` and create the implementation of the
``TransportInterface`` that delivers your message to end point sms gateway.
You can refer my [WebSMS](https://github.com/scaytrase/symfony-websms-bundle) gateway implementation as a reference.
 
 
### Example

```php
class MyMessage implements ShortMessageInterface { /*...*/ }

class SmsController extends Controller {

  public function sendSmsAction()
  {
    $message = new MyMessage('5552368','Help!')
    $sender = $this->get('sms_delivery.sender');
    $sender->spoolMessage($message);
    $result = $sender->flush();
    return new Response('Delivery '. $result ? 'successful' : 'failed');
  }
}
```


### Standalone usage

Despite of the fact that this library is designed as Symfony bundle it could be used as standalone library for sending
short messages. You should just instantiate sender service on your own.

```php

    class MyProviderSmsTransport implements TransportInterface { /*...*/ }

    class MyMessage implements ShortMessageInterface { /*...*/ }

    $transport = new MyProviderSmsTransport();
    $sender = new MessageDeliveryService($transport);
    $sender->spoolMessage(new MyMessage('Message body'));
    $sender->flush(); // Default InstantSpool does not actually needs flushing but you can use another spool instead

```
