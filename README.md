<p align="center"><a href="https://www.aphiria.com" target="_blank" title="Aphiria"><img src="https://www.aphiria.com/images/aphiria-logo.svg" width="200" height="56"></a></p>

<p align="center">
<a href="https://github.com/aphiria/app/actions"><img src="https://github.com/aphiria/app/workflows/ci/badge.svg"></a><a href="https://coveralls.io/github/aphiria/app?branch=1.x"><img src="https://coveralls.io/repos/github/aphiria/app/badge.svg?branch=1.x" alt="Coverage Status"></a>
<a href="https://psalm.dev"><img src="https://shepherd.dev/github/aphiria/app/level.svg"></a>
<a href="https://packagist.org/packages/aphiria/app"><img src="https://poser.pugx.org/aphiria/app/v/stable.svg"></a>
<a href="https://packagist.org/packages/aphiria/app"><img src="https://poser.pugx.org/aphiria/app/v/unstable.svg"></a>
<a href="https://packagist.org/packages/aphiria/app"><img src="https://poser.pugx.org/aphiria/app/license.svg"></a>
</p>

> **Note:** This framework is not stable yet.

This application is a useful starting point for projects that use the Aphiria framework.  Check out this repository, and get started building your own REST API.

## Installation

Aphiria can be installed using Composer:

```bash
composer create-project aphiria/app --prefer-dist --stability dev
```

## Running Locally

You can run your app locally (defaults to http://localhost:8080):

```php
php aphiria app:serve
```

## Demo

This app comes with a simple demo that can store, retrieve, and authenticate users from a local SQLite database.

## Learn More

To learn more about how to use Aphiria, [read its documentation](https://www.aphiria.com/docs/1.x/introduction.html).
