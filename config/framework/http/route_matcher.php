<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

use Aphiria\Routing\Builders\RouteBuilderRegistry;
use Aphiria\Routing\Matchers\Trees\Caching\FileTrieCache;
use Aphiria\Routing\Matchers\Trees\TrieFactory;
use Aphiria\Routing\Matchers\Trees\TrieRouteMatcher;
use Aphiria\Routing\RouteFactory;
use Opulence\Environments\Environment;

/**
 * ----------------------------------------------------------
 * Set up the route matcher
 * ----------------------------------------------------------
 *
 * Note:  The routes should be defined in routes.php
 */
$routeFactory = new RouteFactory(
    function (RouteBuilderRegistry $routes) use ($paths) {
        require_once "{$paths['config.http']}/routes.php";
    }
);

// In non-production environments, we recompile the trie to make sure we have the latest routes
if (Environment::getVar('ENV_NAME') === Environment::PRODUCTION) {
    $trieCache = new FileTrieCache("{$paths['routes.cache']}/trie.cache.txt");
} else {
    $trieCache = null;
}

$trieFactory = new TrieFactory($routeFactory, $trieCache);

return new TrieRouteMatcher($trieFactory->createTrie());
