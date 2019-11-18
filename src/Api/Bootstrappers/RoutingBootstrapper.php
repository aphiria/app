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
use Aphiria\RouteAnnotations\AnnotationRouteRegistrant;
use Aphiria\Routing\AggregateRouteRegistrant;
use Aphiria\Routing\Caching\CachedRouteRegistrant;
use Aphiria\Routing\Caching\FileRouteCache;
use Aphiria\Routing\IRouteRegistrant;
use Aphiria\Routing\Matchers\IRouteMatcher;
use Aphiria\Routing\Matchers\TrieRouteMatcher;
use Aphiria\Routing\RouteCollection;
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

        if (getenv('APP_ENV') === 'production') {
            $routeCache = new FileRouteCache(__DIR__ . '/../../../tmp/framework/http/routeCache.txt');
            $trieCache = new FileTrieCache(__DIR__ . '/../../../tmp/framework/http/trieCache.txt');
            $routeRegistrant = new CachedRouteRegistrant($routeCache);
            $container->bindInstance([CachedRouteRegistrant::class, AggregateRouteRegistrant::class], $routeRegistrant);
        } else {
            $trieCache = null;
            $routeRegistrant = new AggregateRouteRegistrant();
            $container->bindInstance(AggregateRouteRegistrant::class, $routeRegistrant);
        }

        // Bind as a factory so that our app builders can register all routes prior to the routes being built
        $container->bindFactory(
            [IRouteMatcher::class, TrieRouteMatcher::class],
            function () use ($routes, $routeRegistrant, $trieCache) {
                /** @var IRouteRegistrant $routeRegistrant */
                $routeRegistrant->registerRoutes($routes);

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
