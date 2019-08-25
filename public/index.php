<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

use Aphiria\Configuration\ApplicationBuilder;
use Aphiria\DependencyInjection\Bootstrappers\IBootstrapperDispatcher;
use Aphiria\DependencyInjection\Bootstrappers\Inspection\BindingInspectorBootstrapperDispatcher;
use Aphiria\DependencyInjection\Bootstrappers\Inspection\Caching\FileBootstrapperBindingCache;
use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\RequestFactory;
use Aphiria\Net\Http\StreamResponseWriter;
use App\Config;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ----------------------------------------------------------
 * Load environment config files
 * ----------------------------------------------------------
 */
(new Dotenv)->loadEnv(__DIR__ . '/../.env');

/**
 * ----------------------------------------------------------
 * Set up the DI container
 * ----------------------------------------------------------
 */
$container = new Container();
$container->bindInstance([IContainer::class, Container::class], $container);
$bootstrapperDispatcher = new BindingInspectorBootstrapperDispatcher(
    $container,
    $_ENV['APP_ENV'] === 'production'
        ? new FileBootstrapperBindingCache(__DIR__ . '/../tmp/framework/bootstrapperInspections.txt')
        : null
);
$container->bindInstance(IBootstrapperDispatcher::class, $bootstrapperDispatcher);

/**
 * ----------------------------------------------------------
 * Build and run our application
 * ----------------------------------------------------------
 */
$appBuilder = new ApplicationBuilder($container, $bootstrapperDispatcher);
(new Config($appBuilder, $container))->configure();
$app = $appBuilder->buildApiApplication();
$request = (new RequestFactory)->createRequestFromSuperglobals($_SERVER);
$container->bindInstance(IHttpRequestMessage::class, $request);
$response = $app->handle($request);
(new StreamResponseWriter)->writeResponse($response);
