<?php
/*
 * Opulence
 * 
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2018 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Opulence\Api\Exceptions\ExceptionHandler;
use Opulence\Api\Exceptions\ExceptionResponseFactory;
use Opulence\Api\Exceptions\ExceptionResponseFactoryRegistry;
use Opulence\Net\Http\HttpException;
use Opulence\Net\Http\RequestContext;
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
 */
$exceptionResponseFactoryRegistry = new ExceptionResponseFactoryRegistry();
$exceptionResponseFactoryRegistry->registerFactories([
    HttpException::class => function (HttpException $ex, RequestContext $requestContext) {
        return $ex->getResponse();
    }
]);
$exceptionResponseFactory = new ExceptionResponseFactory($exceptionResponseFactoryRegistry);

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
    $logger,
    $exceptionResponseFactory,
    new ResponseWriter(),
    $loggedLevels,
    $thrownLevels,
    $exceptionsNotLogged
);
$exceptionHandler->registerWithPhp();

return $exceptionHandler;