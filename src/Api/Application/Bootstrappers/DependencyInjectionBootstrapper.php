<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Api\Application\Bootstrappers;

use Aphiria\Api\ContainerDependencyResolver;
use Aphiria\Api\IDependencyResolver;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\IBootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Inspection\BindingInspectorBootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Inspection\Caching\FileBootstrapperBindingCache;
use Opulence\Ioc\IContainer;

/**
 * Defines the dependency injection bootstrapper
 */
final class DependencyInjectionBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $bootstrapperDispatcher = new BindingInspectorBootstrapperDispatcher(
            $container,
            Environment::getVar('ENV_NAME') === Environment::PRODUCTION
                ? new FileBootstrapperBindingCache(__DIR__ . '/../tmp/framework/bootstrapperInspections.txt')
                : null
        );
        $container->bindInstance(IBootstrapperDispatcher::class, $bootstrapperDispatcher);
        $container->bindInstance(IDependencyResolver::class, new ContainerDependencyResolver($container));
    }
}
