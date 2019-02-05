<?php

/*
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

use Aphiria\Api\Exceptions\ExceptionHandler;
use Aphiria\Api\Exceptions\ExceptionResponseFactory;
use Aphiria\Api\Exceptions\ExceptionResponseFactoryRegistry;
use Aphiria\Net\Http\ContentNegotiation\NegotiatedResponseFactory;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\StreamResponseWriter;

/**
 * ----------------------------------------------------------
 * Create the exception response factory
 * ----------------------------------------------------------
 *
 * Register any custom exception response factories, keyed by exception type
 *
 * @var NegotiatedResponseFactory $negotiatedResponseFactory
 */
$exceptionResponseFactoryRegistry = new ExceptionResponseFactoryRegistry();
$exceptionResponseFactoryRegistry->registerFactories([
    HttpException::class => function (HttpException $ex, IHttpRequestMessage $request) {
        return $ex->getResponse();
    }
]);
$exceptionResponseFactory = new ExceptionResponseFactory(
    $negotiatedResponseFactory,
    $exceptionResponseFactoryRegistry
);

/**
 * ----------------------------------------------------------
 * Logged error levels
 * ----------------------------------------------------------
 *
 * Specify the bitwise value of error levels to log
 */
$loggedLevels = 0;

/**
 * ----------------------------------------------------------
 * Thrown error levels
 * ----------------------------------------------------------
 *
 * Specify the error levels to rethrow as exceptions
 */
$thrownLevels = (E_ALL & ~(E_DEPRECATED | E_USER_DEPRECATED));

/**
 * ----------------------------------------------------------
 * Exceptions to not log
 * ----------------------------------------------------------
 *
 * These exceptions will not be logged
 */
$exceptionsNotLogged = [
    //
];

/**
 * ----------------------------------------------------------
 * Create the application exception handler
 * ----------------------------------------------------------
 */
$exceptionHandler = new ExceptionHandler(
    $exceptionResponseFactory,
    require "{$paths['config.framework']}/logger.php",
    $loggedLevels,
    $thrownLevels,
    $exceptionsNotLogged,
    new StreamResponseWriter()
);
$exceptionHandler->registerWithPhp();

return $exceptionHandler;
