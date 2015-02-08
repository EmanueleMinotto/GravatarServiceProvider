Gravatar Service Provider
=========================

[![Build Status](https://img.shields.io/travis/EmanueleMinotto/GravatarServiceProvider.svg?style=flat)](https://travis-ci.org/EmanueleMinotto/GravatarServiceProvider)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/876b86d7-429d-429d-9b01-07f01f2243df.svg?style=flat)](https://insight.sensiolabs.com/projects/876b86d7-429d-429d-9b01-07f01f2243df)
[![Coverage Status](https://img.shields.io/coveralls/EmanueleMinotto/GravatarServiceProvider.svg?style=flat)](https://coveralls.io/r/EmanueleMinotto/GravatarServiceProvider)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/EmanueleMinotto/GravatarServiceProvider.svg?style=flat)](https://scrutinizer-ci.com/g/EmanueleMinotto/GravatarServiceProvider/)
[![Total Downloads](https://img.shields.io/packagist/dt/emanueleminotto/gravatar-service-provider.svg?style=flat)](https://packagist.org/packages/emanueleminotto/gravatar-service-provider)

A [Gravatar](http://www.gravatar.com) service provider for [Silex](http://silex.sensiolabs.org/).

API: [emanueleminotto.github.io/GravatarServiceProvider](http://emanueleminotto.github.io/GravatarServiceProvider/)

## Install
Install Silex using [Composer](http://getcomposer.org/).

Install the GravatarServiceProvider adding `emanueleminotto/gravatar-service-provider` to your composer.json or from CLI:

```
$ composer require emanueleminotto/gravatar-service-provider
```

## Usage

Initialize it using `register`

```php
use EmanueleMinotto\GravatarServiceProvider\GravatarServiceProvider;

$app->register(new GravatarServiceProvider(), array(
    'gravatar.http_client' => new GuzzleHttp\Client(), // default null, optional
    'gravatar.twig' => false, // default true, optional
));
```

The `gravatar.http_client` can be replaced by an instance of the [Guzzle](http://docs.guzzlephp.org/en/latest/) HTTP client,
by default a new instance is provided.

From PHP
```php
$app->get('/hello/{email}', function ($email) use ($app) {
    $profile = $app['gravatar']->getProfile($email);

    return 'Hello ' . $profile['preferredUsername'];
});
```

From [Twig](http://twig.sensiolabs.org/)

Setting the option `gravatar.twig => true`, if there's the [Twig service provider](http://silex.sensiolabs.org/doc/providers/twig.html), you'll be able to use the [Twig extension](https://github.com/EmanueleMinotto/Gravatar#twig-extension) provided by the [Gravatar library](https://github.com/EmanueleMinotto/Gravatar).
