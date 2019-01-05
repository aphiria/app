<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Opulence\Api\Exceptions\ExceptionHandler;
use Opulence\Api\Exceptions\ExceptionResponseFactory;
use Opulence\Api\Exceptions\ExceptionResponseFactoryRegistry;
use Opulence\Net\Http\ContentNegotiation\NegotiatedResponseFactory;
use Opulence\Net\Http\HttpException;
use Opulence\Net\Http\IHttpRequestMessage;
use Opulence\Net\Http\ResponseWriter;

/**
 * ----------------------------------------------------------
 * Create a PSR-3 logger
 * ----------------------------------------------------------
 *
 * Note: You may use any PSR-3 logger you'd like
 * For convenience, the Monolog library is included here
 */
$logger = new Logger('app');
$logger->pushHandler(new ErrorLogHandler());

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
    $logger,
    $loggedLevels,
    $thrownLevels,
    $exceptionsNotLogged,
    new ResponseWriter()
);
$exceptionHandler->registerWithPhp();

return $exceptionHandler;
