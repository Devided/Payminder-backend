[![Build Status](https://scrutinizer-ci.com/g/thomasvargiu/TmvWhatsApi/badges/build.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/TmvWhatsApi/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/thomasvargiu/TmvWhatsApi/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/TmvWhatsApi/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thomasvargiu/TmvWhatsApi/badges/quality-score.png?s=c66994bc72499c4771de0e22fb8f257b75685552)](https://scrutinizer-ci.com/g/thomasvargiu/TmvWhatsApi/)
[![Dependency Status](https://www.versioneye.com/user/projects/545f82008683321bc8000036/badge.svg?style=flat)](https://www.versioneye.com/user/projects/545f82008683321bc8000036)
[![Latest Stable Version](https://poser.pugx.org/thomasvargiu/tmv-whatsapi/v/stable.svg)](https://packagist.org/packages/thomasvargiu/tmv-whatsapi)
[![Total Downloads](https://poser.pugx.org/thomasvargiu/tmv-whatsapi/downloads.svg)](https://packagist.org/packages/thomasvargiu/tmv-whatsapi)
[![Latest Unstable Version](https://poser.pugx.org/thomasvargiu/tmv-whatsapi/v/unstable.svg)](https://packagist.org/packages/thomasvargiu/tmv-whatsapi)
[![License](https://poser.pugx.org/thomasvargiu/tmv-whatsapi/license.svg)](https://packagist.org/packages/thomasvargiu/tmv-whatsapi)

# WhatsAPI

**Status: development**

**Last update: 13/11/2014 (See changelist below)**

**Do not use it in production environment!**


## About WhatsAPI

WhatsAPI is a client library to use Whatsapp services.

This is a new project based on the original WhatsAPI:
Please see [the original project](https://github.com/venomous0x/WhatsAPI)
or the new [WhatsApi Official](https://github.com/mgp25/WhatsAPI-Official)

## Why a new project?

The original WhatsAPI library is not compatible with composer, no PSR compatible, and it's very old.
I want to develop this new library in order to make it more usable.
If you want to help, just do it :)

## How to start using this library

(Everything can be changed in the future)

### Number registration ###

First, you need an Identity. This string identify a device. You can generate one like this:
```php
use Tmv\WhatsApi\Service\IdentityService;

$identityId = IdentityService::generateIdentity();
```

You need it everytime to connect to the service, so you should save it to identify your device (you can use ```urlencode``` and ```urldecode``` to save it).

Now, you can request a code to verify your number:

```php
use Tmv\WhatsApi\Service\LocalizationService;
use Tmv\WhatsApi\Service\IdentityService;
use Tmv\WhatsApi\Service\PcntlListener;
use Tmv\WhatsApi\Service\MediaService;
use Tmv\WhatsApi\Entity\Phone;
use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Client;
use Tmv\WhatsApi\Options;
use Zend\EventManager\EventInterface;

// Initializing client
// Creating a service to retrieve phone info
$localizationService = new LocalizationService();
$localizationService->setCountriesPath(__DIR__ . '/data/countries.csv');
$identityService = new IdentityService();
$identityService->setNetworkInfoPath(__DIR__ . '/data/networkinfo.csv');

// Creating a phone object...
$phone = new Phone(''); // Your number with international prefix (without ```+``` or ```00```)
// Injecting phone properties
$localizationService->injectPhoneProperties($phone);

$identity = new Identity();
$identity->setNickname($nickname); // Nickname to use for notifications
$identity->setIdentityToken($identityId); // Previously generated identity
$identity->setPhone($phone);

$res = $identityService->codeRequest($identity);
/*
var_dump() of $res:
array(4) {
  'status' =>
  string(4) "sent"
  'length' =>
  int(6)
  'method' =>
  string(3) "sms"
  'retry_after' =>
  int(25205)
}
*/
```

If an error occurred, an exception will be thrown. If ok, an sms with a verify code will be sended to your number.
The second parameter of ```IdentityService::codeRequest()```can accept two strings:
- ```sms```: an SMS will be sent to your number with a code
- ```voice```: you will be called and a voice will tell you the code

Now you need to insert the code:

```php
// ...
$res = $identityService->codeRegister($identity, $code);
/*var_dump() of $res:
array(10) {
  'status' =>
  string(2) "ok"
  'login' =>
  string(12) "39123456789"
  'pw' =>
  string(28) "xxxxxxxxxxxxxxx="
  'type' =>
  string(3) "new"
  'expiration' =>
  int(1454786892)
  'kind' =>
  string(4) "free"
  'price' =>
  string(8) "€ 0,89"
  'cost' =>
  string(4) "0.89"
  'currency' =>
  string(3) "EUR"
  'price_expiration' =>
  int(1426348327)
}
*/
```

The result is simple, you can understand it. The most important key is ```pw```. This is your password, save it!


### Initializing client ###

```php
use Tmv\WhatsApi\Service\LocalizationService;
use Tmv\WhatsApi\Entity\Phone;
use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Client;
use Tmv\WhatsApi\Service\PcntlListener;
use Tmv\WhatsApi\Service\MediaService;
use Tmv\WhatsApi\Options;
use Zend\EventManager\EventInterface;

// Initializing client
// Creating a service to retrieve phone info
$localizationService = new LocalizationService();
$localizationService->setCountriesPath(__DIR__ . '/data/countries.csv');

// Creating a phone object...
$phone = new Phone(''); // your phone number with international prefix
// Injecting phone properties
$localizationService->injectPhoneProperties($phone);

// Creating identity
$identity = new Identity();
$identity->setNickname(''); // your name
$identity->setIdentityToken('');    // your token
$identity->setPassword(''); // your password
$identity->setPhone($phone);

// Initializing client
$client = new Client($identity);
$client->setChallengeDataFilepath(__DIR__ . '/data/nextChallenge.dat');

// Attach PCNTL listener to handle signals (if you have PCNTL extension)
// This allow to kill process softly
$pcntlListener = new PcntlListener();
$client->getEventManager()->attach($pcntlListener);

// Creating MediaService for media messages
$mediaServiceOptions = new Options\MediaService();
$mediaServiceOptions->setMediaFolder(sys_get_temp_dir());
$mediaServiceOptions->setDefaultImageIconFilepath(__DIR__ . '/data/ImageIcon.jpg');
$mediaServiceOptions->setDefaultVideoIconFilepath(__DIR__ . '/data/VideoIcon.jpg');
$mediaService = new MediaService($mediaServiceOptions);
$client->setMediaService($mediaService);

// Attaching events...
// ...

$client->getEventManager()->attach('onConnected', function(EventInterface $e) {
    /** @var Client $client */
    $client = $e->getTarget();

    // Actions
    // ...
});

// Connect, login and process messages
// Automatically send presence every 10 seconds
$client->run();
```

### Sending a message ###

```php
use Tmv\WhatsApi\Message\Action;
use Tmv\WhatsApi\Entity\MediaFileInterface;

$number = ''; // number to send message
// Sending composing notification (simulating typing)
$client->send(new Action\ChatState($number, Action\ChatState::STATE_COMPOSING));
// Sending paused notification (typing end)
$client->send(new Action\ChatState($number, Action\ChatState::STATE_PAUSED));

// Creating text message action
$message = new Action\MessageText($identity->getNickname(), $number);
$message->setBody('Hello');

// OR: creating media (image, video, audio) message (beta)
$mediaFile = $client->getMediaService()
    ->getMediaFileFactory()
    ->factory('/path/to/image.png', MediaFileInterface::TYPE_IMAGE);
$message = new Action\MessageMedia();
$message->setTo($number)
    ->setMediaFile($mediaFile);

// Sending message...
$client->send($message);
```

### Receiving message ###

```php
use Tmv\WhatsApi\Event\MessageReceivedEvent;
use Tmv\WhatsApi\Message\Received;

// onMessageReceived event
$client->getEventManager()->attach(
    'onMessageReceived',
    function (MessageReceivedEvent $e) {
        $message = $e->getMessage();
        echo str_repeat('-', 80) . PHP_EOL;
        echo '** MESSAGE RECEIVED **' . PHP_EOL;
        echo sprintf('From: %s', $message->getFrom()) . PHP_EOL;
        if ($message->isFromGroup()) {
            echo sprintf('Group: %s', $message->getGroupId()) . PHP_EOL;
        }
        echo sprintf('Date: %s', $message->getDateTime()->format('Y-m-d H:i:s')) . PHP_EOL;

        if ($message instanceof Received\MessageText) {
            echo PHP_EOL;
            echo sprintf('%s', $message->getBody()) . PHP_EOL;
        } elseif ($message instanceof Received\MessageMedia) {
            echo sprintf('Type: %s', $message->getMedia()->getType()) . PHP_EOL;
        }
        echo str_repeat('-', 80) . PHP_EOL;
    }
);
```

### Debugging ###

It's possible to debug attaching events. It's possible to listen all events attaching to '*' event.

```php
use Zend\EventManager\EventInterface;

// Debug events
$client->getEventManager()->attach(
    'node.received',
    function (EventInterface $e) {
        $node = $e->getParam('node');
        echo sprintf("\n--- Node received:\n%s\n", $node);
    }
);
$client->getEventManager()->attach(
    'node.send.pre',
    function (EventInterface $e) {
        $node = $e->getParam('node');
        echo sprintf("\n--- Sending Node:\n%s\n", $node);
    }
);
```

## Public Events ##

- onMessageReceived (generic event for all messages)
- onMessageTextReceived
- onMessageMediaImageReceived
- onMessageMediaAudioReceived
- onMessageMediaVideoReceived
- onMessageMediaVcardReceived
- onMessageMediaLocationReceived
- onConnected
- onLoginFailed
- onReceiptServer
- onReceiptClient
- onPresenceReceived
- onGroupParticipantAdded
- onGroupParticipantRemoved
- onGetGroupsResult
- onGetGroupInfoResult

## Changelist ##

### 13 November 2014 ###

- Icons generation for images and videos
- Optional dependency suggested in composer for video icons generation

### 9 November 2014 ###

- Added MessageMedia action to send image, video and audio messages (generated icons are not supported yet)