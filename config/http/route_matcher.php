<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

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
    function (RouteBuilderRegistry $routes) {
        require_once __DIR__ . '/routes.php';
    }
);
$trieCache = new FileTrieCache(__DIR__ . '/../../tmp/framework/http/routing/trie.cache.txt');
$trieFactory = new TrieFactory($routeFactory, $trieCache);

return new TrieRouteMatcher($trieFactory->createTrie());
