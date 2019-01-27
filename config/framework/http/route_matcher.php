<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

use Opulence\Environments\Environment;
use Opulence\Routing\Builders\RouteBuilderRegistry;
use Opulence\Routing\Matchers\Trees\Caching\FileTrieCache;
use Opulence\Routing\Matchers\Trees\TrieFactory;
use Opulence\Routing\Matchers\Trees\TrieRouteMatcher;
use Opulence\Routing\RouteFactory;

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
