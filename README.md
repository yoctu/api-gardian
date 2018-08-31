# API guardian

[![Build Status](https://travis-ci.org/yoctu/api-guardian.svg?branch=master)](https://travis-ci.org/yoctu/api-guardian)

Protect your pages with a token!

### Prerequisites

You will need a project running Objective-PHP to use this package.

### Installing

The package should be easy to install. You just need to add this repository to your composer.json

```
composer require yoctu/api-guardian
```

And add the package to your Application.php with filter(s) if needed.

```
$this->getStep('auth')
    ->plug(Apiguardian::class, new UrlFilter('/api/*'))->as('api-guardian')
;
```

## Using the package
Now that the package has been plugged on your application 
you can setup the one or more token to be used in the configuration.

```
return [
    new Param('api-keys', ['api_key_one', 'api_key_two'])
];
```

You'll now need to add a token to your request to pass the middleware

```
GET / HTTP/1.1
Accept: */*
Accept-Encoding: gzip, deflate
Authorization: api_key_one
Connection: keep-alive
```

## Running the tests

`./vendor/bin/phpunit --bootstrap vendor/autoload.php tests`

## License

This project is licensed under the GNU GPL 3.0 License - see the [LICENSE](LICENSE) file for details
