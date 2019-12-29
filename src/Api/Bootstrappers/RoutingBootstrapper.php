<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Api\Bootstrappers;

use Aphiria\DependencyInjection\Bootstrappers\Bootstrapper;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Routing\Annotations\AnnotationRouteRegistrant;
use Aphiria\Routing\Caching\CachedRouteRegistrant;
use Aphiria\Routing\Caching\FileRouteCache;
use Aphiria\Routing\Matchers\IRouteMatcher;
use Aphiria\Routing\Matchers\TrieRouteMatcher;
use Aphiria\Routing\RouteCollection;
use Aphiria\Routing\RouteRegistrantCollection;
use Aphiria\Routing\UriTemplates\AstRouteUriFactory;
use Aphiria\Routing\UriTemplates\Compilers\Tries\Caching\FileTrieCache;
use Aphiria\Routing\UriTemplates\Compilers\Tries\TrieFactory;
use Aphiria\Routing\UriTemplates\IRouteUriFactory;

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
        $routes = new RouteCollection();
        $container->bindInstance(RouteCollection::class, $routes);
        $routeRegistrants = new RouteRegistrantCollection();
        $container->bindInstance(RouteRegistrantCollection::class, $routeRegistrants);

        if (getenv('APP_ENV') === 'production') {
            $trieCache = new FileTrieCache(__DIR__ . '/../../../tmp/framework/http/trieCache.txt');
            $routeCache = new FileRouteCache(__DIR__ . '/../../../tmp/framework/http/routeCache.txt');
            $cachedRouteRegistrant = new CachedRouteRegistrant($routeCache, $routeRegistrants);
            $container->bindInstance(CachedRouteRegistrant::class, $cachedRouteRegistrant);
        } else {
            $trieCache = $cachedRouteRegistrant = null;
        }

        // Bind as a factory so that our app builders can register all routes prior to the routes being built
        $container->bindFactory(
            [IRouteMatcher::class, TrieRouteMatcher::class],
            function () use ($routes, $cachedRouteRegistrant, $routeRegistrants, $trieCache) {
                // Always defer to using cached routes if they exist
                if ($cachedRouteRegistrant instanceof CachedRouteRegistrant) {
                    $cachedRouteRegistrant->registerRoutes($routes);
                } else {
                    /** @var RouteRegistrantCollection $routeRegistrants */
                    $routeRegistrants->registerRoutes($routes);
                }

                return new TrieRouteMatcher((new TrieFactory($routes, $trieCache))->createTrie());
            },
            true
        );

        $container->bindInstance(IRouteUriFactory::class, new AstRouteUriFactory($routes));

        // Register some route annotation dependencies
        $routeAnnotationRegistrant = new AnnotationRouteRegistrant(__DIR__ . '/../..');
        $container->bindInstance(AnnotationRouteRegistrant::class, $routeAnnotationRegistrant);
    }
}
