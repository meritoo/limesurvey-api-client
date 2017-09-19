# Meritoo LimeSurvey API Client
Client of the [LimeSurvey's API](https://manual.limesurvey.org/RemoteControl_2_API).

[![Travis](https://img.shields.io/travis/rust-lang/rust.svg?style=flat-square)](https://travis-ci.org/meritoo/limesurvey-api-client) [![Packagist](https://img.shields.io/packagist/v/meritoo/limesurvey-api-client.svg?style=flat-square)](https://packagist.org/packages/meritoo/limesurvey-api-client) [![StyleCI](https://styleci.io/repos/104114960/shield?branch=master)](https://styleci.io/repos/104114960) [![license](https://img.shields.io/github/license/meritoo/limesurvey-api-client.svg?style=flat-square)](https://github.com/meritoo/limesurvey-api-client) [![GitHub commits](https://img.shields.io/github/commits-since/meritoo/limesurvey-api-client/0.0.1.svg?style=flat-square)](https://github.com/meritoo/limesurvey-api-client) [![Coverage Status](https://coveralls.io/repos/github/meritoo/limesurvey-api-client/badge.svg?branch=master)](https://coveralls.io/github/meritoo/limesurvey-api-client?branch=master)

## Installation

Run [Composer](https://getcomposer.org) to install this package in your project:

    ```bash
    $ composer require meritoo/limesurvey-api-client
    ```

> How to install Composer: https://getcomposer.org/download

## Usage

1. First of all you have to prepare configuration of connection and create instance of a client:

    ```php
    use Meritoo\LimeSurvey\ApiClient\Client\Client;
    use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;
    use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

    /*
     * Prepare configuration of connection and client of the API
     */
    $configuration = new ConnectionConfiguration('http://test.com', 'test', 'test');
    $client = new Client($configuration);
    ```

2. Next run the method which you would like:

    ```php
    /*
     * Run required method
     */
    $result = $client->run(MethodType::LIST_SURVEYS);
    ```

3. Finally grab data from result of called method:

    ```php
    /*
     * ...and grab data from the result
     */
    $data = $result->getData();
    ```

Full code of this example:

```php
use Meritoo\LimeSurvey\ApiClient\Client\Client;
use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/*
 * Prepare configuration of connection and client of the API
 */
$configuration = new ConnectionConfiguration('http://test.com', 'test', 'test');
$client = new Client($configuration);

/*
 * Run required method and grab data from the result
 */
$result = $client->run(MethodType::LIST_SURVEYS);
$data = $result->getData();
```

## Available methods

All available methods provides `Meritoo\LimeSurvey\ApiClient\Type\MethodType` class as constants of the class. Examples:

```php
// Add a response to the survey responses collection
MethodType::ADD_RESPONSE;

// The IDs and properties of token/participants of a survey
MethodType::LIST_PARTICIPANTS;

// List the surveys belonging to a user
MethodType::LIST_SURVEYS;
```

Name of the method, actually constant of the `MethodType` class, you should pass as 1st argument of `\Meritoo\LimeSurvey\ApiClient\Client\Client::run()` method. Example:

```php
$client->run(MethodType::GET_PARTICIPANT_PROPERTIES);
```

## Debug mode

In some cases more information may be required to fix bugs. The "debug" mode will help you do this. You can turn it on while preparing configuration of connection by passing `true` as 4th argument of constructor:

```php
use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;
$configuration = new ConnectionConfiguration('http://test.com', 'test', 'test', true);
```

The "debug" mode can be turned on if the instance of configuration exists by using the `\Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration::setDebugMode()` method:

```php
$configuration->setDebugMode(true);
```

If you want to verify if if the "debug" mode is turned on simply call the `\Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration::isDebugModeOn()` method:

```php
$debugMode = $configuration->isDebugModeOn();
```

##  Getting data from result

#### Verify if the result is empty

First of all you have to call required method to get result - instance of `\Meritoo\LimeSurvey\ApiClient\Result\Result` class. The result allows you to get information if there is any data by calling the `\Meritoo\LimeSurvey\ApiClient\Result\Result::isEmpty()` method:

```php
use Meritoo\LimeSurvey\ApiClient\Result\Result;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

$result = new Result(MethodType::LIST_SURVEYS, []);
$isEmpty = $result->isEmpty();

var_dump($isEmpty); // bool(true)
```

#### Prepared/processed vs raw data

Result allows you to get data, the essence of calling API's method by calling the `\Meritoo\LimeSurvey\ApiClient\Result\Result::getData()` method. This method accepts 1 bool argument:
- `false` - (default) prepared/processed data provided will be returned
- `true` - raw data will be returned

Prepared/processed data means instances of classes from `Meritoo\LimeSurvey\ApiClient\Result\Item\*` namespace.

> Attention.
> 1. The above is true, if result provided by the API *is iterable*. Otherwise - instance of single item is returned.
> 2. Methods that provides iterable result:
>
>	- MethodType::LIST_PARTICIPANTS
>	- MethodType::LIST_QUESTIONS
>	- MethodType::LIST_SURVEYS
>	- MethodType::LIST_USERS
>
>	They are defined in `Meritoo\LimeSurvey\ApiClient\Type\MethodType::isResultIterable()` method.

#### Prepared/processed data

All instances are returned as elements of collection (instance of `Meritoo\Common\Collection\Collection` class). Example:

```php
class Meritoo\Common\Collection\Collection#565 (1) {
  private $elements =>
  array(2) {
    [0] =>
    class Meritoo\LimeSurvey\ApiClient\Result\Item\Survey#564 (5) {
      private $id =>
      int(456)
      private $title =>
      string(12) "Another Test"
      private $expiresAt =>
      NULL
      private $active =>
      bool(true)
    }
    [1] =>
    class Meritoo\LimeSurvey\ApiClient\Result\Item\Survey#564 (5) {
      private $id =>
      int(456)
      private $title =>
      string(12) "Another Test"
      private $expiresAt =>
      NULL
      private $active =>
      bool(true)
    }
  }
}
```

If result provided by the API *is not iterable*, as mentioned above, instance of single item is returned. Example:

```php
class Meritoo\LimeSurvey\ApiClient\Result\Item\Participant#701 (17) {
  private $id =>
  int(123)
  private $participantId =>
  int(456)
  private $mpId =>
  NULL
  private $firstName =>
  string(5) "Lorem"
  private $lastName =>
  string(5) "Ipsum"
  (...)
}
```

#### Raw data

An array with scalars or other arrays. Example:

```php
array(2) {
  [0] =>
  array(5) {
    'sid' =>
    string(3) "123"
    'surveyls_title' =>
    string(4) "Test"
    'startdate' =>
    NULL
    'expires' =>
    string(19) "2017-09-19 13:02:41"
    'active' =>
    string(1) "N"
  }
  [1] =>
  array(5) {
    'sid' =>
    string(3) "456"
    'surveyls_title' =>
    string(12) "Another Test"
    'startdate' =>
    string(19) "2017-09-19 13:02:41"
    'expires' =>
    NULL
    'active' =>
    string(1) "Y"
  }
}
```

## Links

- LimeSurvey:
  https://www.limesurvey.org
- Composer:
  https://getcomposer.org

Enjoy!
