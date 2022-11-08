# Form validation

This project aims to make validation for POST and GET requests easier. The way we get this done is by making the way validation work similar to the input tag of HTML.

## Getting Started

These instructions will give you a working project with this library.

### Prerequisites

Requirements for the software and other tools to build, test and push
- PHP 7.4 or higher
- Composer

### Installing

A step by step series of examples that tell you how to get a development
environment running.

Start a composer project

    Composer init

Install the library with composer

    Composer install mitchellston/form_validation

Add the library to the composers autoloader (in composer.json)

```json
{
  "autoload": {
    "classmap": [
      "./vendor/mitchellston/"
    ]
  }
}
```
Update composer autoloader

    Composer update

### Demo

```php
<?php
//Let's pretend the url is https://www.test.com?username=carl
require_once "../vendor/autoload.php";
use PostGetRequestValidation\Attributes;
use PostGetRequestValidation\Methods;
use PostGetRequestValidation\Types;
use PostGetRequestValidation\Validation;

$test = new Validation("username", Methods::GET, Types::TEXT, 
[
    Attributes::minLength => ["value" => 5, "errorMessage" => "A username needs to at least be 5 characters long!"]
]);
echo $test->getValue(); # carl
echo $test->getErrors(); # ["A username needs to at least be 5 characters long!"]
//Now you can send back the errors to your users
```

## License

This project is licensed under the [MIT](LICENSE)
 License - see the [LICENSE](LICENSE) file for
details
