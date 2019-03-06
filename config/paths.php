<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

/**
 * ----------------------------------------------------------
 * Define the list of paths needed by this application
 * ----------------------------------------------------------
 */
return [
    /**
     * ----------------------------------------------------------
     * Configs
     * ----------------------------------------------------------
     *
     * "config" => The config directory
     * "config.framework" => The config directory for framework files
     * "config.framework.http" => The HTTP config directory for framework files
     */
    'config' => realpath(__DIR__),
    'config.framework' => realpath(__DIR__ . '/framework'),
    'config.framework.http' => realpath(__DIR__ . '/framework/http'),

    /**
     * ----------------------------------------------------------
     * Logs
     * ----------------------------------------------------------
     *
     * "logs" => The logs directory
     */
    'logs' => realpath(__DIR__ . '/../tmp/logs'),

    /**
     * ----------------------------------------------------------
     * Public
     * ----------------------------------------------------------
     *
     * "public" => The public directory
     */
    'public' => realpath(__DIR__ . '/../public'),

    /**
     * ----------------------------------------------------------
     * Root
     * ----------------------------------------------------------
     *
     * "root" => The root directory
     */
    'root' => realpath(__DIR__ . '/..'),

    /**
     * ----------------------------------------------------------
     * Routes
     * ----------------------------------------------------------
     *
     * "routes.cache" => The cached routes directory
     */
    'routes.cache' => realpath(__DIR__ . '/../tmp/framework/http/routing'),

    /**
     * ----------------------------------------------------------
     * Source
     * ----------------------------------------------------------
     *
     * "src" => The application source directory
     */
    'src' => realpath(__DIR__ . '/../src'),

    /**
     * ----------------------------------------------------------
     * Tests
     * ----------------------------------------------------------
     *
     * "tests" => The tests directory
     */
    'tests' => realpath(__DIR__ . '/../tests/src'),

    /**
     * ----------------------------------------------------------
     * Temporary
     * ----------------------------------------------------------
     *
     * "tmp" => The temporary directory
     * "tmp.framework.http" => The framework's temporary Http directory
     */
    'tmp' => realpath(__DIR__ . '/../tmp'),
    'tmp.framework.http' => realpath(__DIR__ . '/../tmp/framework/http'),

    /**
     * ----------------------------------------------------------
     * Vendor
     * ----------------------------------------------------------
     *
     * "vendor" => The vendor directory
     */
    'vendor' => realpath(__DIR__ . '/../vendor')
];
