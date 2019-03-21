<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Application\Bootstrappers\Http;

use Aphiria\Routing\IRouteFactory;
use Aphiria\Routing\LazyRouteFactory;
use Aphiria\Routing\Matchers\IRouteMatcher;
use Aphiria\Routing\Matchers\Trees\Caching\FileTrieCache;
use Aphiria\Routing\Matchers\Trees\TrieFactory;
use Aphiria\Routing\Matchers\Trees\TrieRouteMatcher;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines a the bootstrapper that binds routing components
 */
final class RoutingBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $routeFactory = new LazyRouteFactory();
        $container->bindInstance([IRouteFactory::class, LazyRouteFactory::class], $routeFactory);
        // Bind as a factory so that our app builders can register all routes prior to the routes being built
        $container->bindFactory(
            [IRouteMatcher::class, TrieRouteMatcher::class],
            function () use ($routeFactory) {
                if (\getenv('ENV_NAME') === Environment::PRODUCTION) {
                    $trieCache = new FileTrieCache(\getenv('TMP_DIR_') . '/framework/http/triecache.txt');
                } else {
                    $trieCache = null;
                }

                $trieFactory = new TrieFactory($routeFactory, $trieCache);

                return new TrieRouteMatcher($trieFactory->createTrie());
            },
            true
        );
    }
}
