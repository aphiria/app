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

You can run your app locally either directly via the built-in PHP web server or via Docker Compose.  Both solutions result in your app being hosted at http://localhost:8080.

### Built-In PHP Web Server

```
php aphiria app:serve
```

### Docker Compose Web Server

This app comes bundled with a Docker Compose setup meant to ease local development.  It is not meant for production, but can get you up and running quickly with an nginx web server running your application along with Xdebug for debugging.  Simply run:

```
docker compose up -d --build app
```

To start debugging with Xdebug, configure your IDE to map your checked out Aphiria code to the _/app_ directory within the php service created by Docker Compose.  Ensure that your IDE is configured to listen to port 9004 for Xdebug connections.

## Demo

This app comes with a simple demo that can store, retrieve, and authenticate users from a local SQLite database.  It uses <a href="https://book.cakephp.org/phinx/0/en/contents.html" target="_blank">Phinx</a> to manage database migrations and seeding, which can be executed with the following commands, respectively:

* `vendor/bin/phinx migrate`
* `vendor/bin/phinx seed:run`

Phinx-specific configuration settings, eg the paths to migration and seed files, are located in _phinx.php_.

## Learn More

To learn more about how to use Aphiria, [read its documentation](https://www.aphiria.com/docs/1.x/introduction.html).
