<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

use Aphiria\Application\BootstrapperCollection;
use Aphiria\Configuration\GlobalConfigurationBuilder;
use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\IServiceResolver;
use Aphiria\Framework\Api\Builders\ApiApplicationBuilder;
use Aphiria\Framework\Configuration\Bootstrappers\ConfigurationBootstrapper;
use Aphiria\Framework\Configuration\Bootstrappers\EnvironmentVariableBootstrapper;
use Aphiria\Framework\Exceptions\Bootstrappers\GlobalExceptionHandlerBootstrapper;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\StreamResponseWriter;
use App\App;
use Doctrine\Common\Annotations\AnnotationRegistry;

$autoloader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);

/**
 * ----------------------------------------------------------
 * Bootstrap the application
 * ----------------------------------------------------------
 */
$container = new Container();
$container->bindInstance([IServiceResolver::class, IContainer::class, Container::class], $container);
Container::$globalInstance = $container;
$globalConfigurationBuilder = (new GlobalConfigurationBuilder)->withEnvironmentVariables()
    ->withPhpFileConfigurationSource(__DIR__ . '/../config.php');
(new BootstrapperCollection)->addMany([
    new EnvironmentVariableBootstrapper(__DIR__ . '/../.env'),
    new ConfigurationBootstrapper($globalConfigurationBuilder),
    new GlobalExceptionHandlerBootstrapper($container)
])->bootstrapAll();

/**
 * ----------------------------------------------------------
 * Build and run our application
 * ----------------------------------------------------------
 */
$app = (new ApiApplicationBuilder($container))->withModule(new App())
    ->build();
$response = $app->handle($container->resolve(IHttpRequestMessage::class));
(new StreamResponseWriter)->writeResponse($response);
