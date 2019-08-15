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
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\RequestFactory;
use Aphiria\Net\Http\StreamResponseWriter;
use App\Config;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\IBootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Inspection\BindingInspectorBootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Inspection\Caching\FileBootstrapperBindingCache;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ----------------------------------------------------------
 * Load environment config files
 * ----------------------------------------------------------
 *
 * Note:  For performance in production, it's highly suggested
 * you set environment variables on the server itself
 */
require __DIR__ . '/../.env.app.php';

/**
 * ----------------------------------------------------------
 * Set up the DI container
 * ----------------------------------------------------------
 */
$container = new Container();
$container->bindInstance([IContainer::class, Container::class], $container);
$bootstrapperDispatcher = new BindingInspectorBootstrapperDispatcher(
    $container,
    Environment::getVar('ENV_NAME') === Environment::PRODUCTION
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
