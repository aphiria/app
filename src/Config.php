<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App;

use Aphiria\Configuration\AphiriaComponentBuilder;
use Aphiria\Configuration\IApplicationBuilder;
use App\Api\Application\Bootstrappers\ContentNegotiatorBootstrapper;
use App\Api\Application\Bootstrappers\DependencyInjectionBootstrapper;
use App\Api\Application\Bootstrappers\ExceptionHandlerBootstrapper;
use App\Api\Application\Bootstrappers\RoutingBootstrapper;
use App\Api\Application\Bootstrappers\SerializerBootstrapper;
use App\Logging\Application\Bootstrappers\LoggerBootstrapper;
use App\Users\Application\Config\UserModuleBuilder;
use Opulence\Ioc\IContainer;

/**
 * Defines the application configuration
 */
final class Config
{
    /** @var IApplicationBuilder The app builder to use when configuring the application */
    private IApplicationBuilder $appBuilder;
    /** @var IContainer The DI container that can resolve dependencies */
    private IContainer $container;

    /**
     * @param IApplicationBuilder $appBuilder The app builder to use when configuring the application
     * @param IContainer $container The DI container that can resolve dependencies
     */
    public function __construct(IApplicationBuilder $appBuilder, IContainer $container)
    {
        $this->appBuilder = $appBuilder;
        $this->container = $container;
    }

    /**
     * Configures the application's modules
     */
    public function configure(): void
    {
        // Configure this app to use Aphiria components
        (new AphiriaComponentBuilder($this->container))
            ->withEncoderComponent($this->appBuilder)
            ->withRoutingComponent($this->appBuilder);

        // Register some global bootstrappers
        $this->appBuilder->withBootstrappers(fn () => [
            new SerializerBootstrapper,
            new DependencyInjectionBootstrapper,
            new ExceptionHandlerBootstrapper,
            new LoggerBootstrapper,
            new ContentNegotiatorBootstrapper,
            new RoutingBootstrapper
        ]);

        // Register any modules
        $this->appBuilder->withModule(new UserModuleBuilder($this->container));
    }
}
