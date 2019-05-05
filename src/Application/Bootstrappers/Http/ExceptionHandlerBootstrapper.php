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

use Aphiria\Api\Exceptions\ExceptionHandler;
use Aphiria\Api\Exceptions\ExceptionLogLevelFactoryRegistry;
use Aphiria\Api\Exceptions\ExceptionResponseFactory;
use Aphiria\Api\Exceptions\ExceptionResponseFactoryRegistry;
use Aphiria\Api\Exceptions\IExceptionHandler;
use Aphiria\Net\Http\ContentNegotiation\INegotiatedResponseFactory;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\StreamResponseWriter;
use App\Application\Bootstrappers\LoggerBootstrapper;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Defines the bootstrapper that registers the exception handler
 */
final class ExceptionHandlerBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        /**
         * ----------------------------------------------------------
         * Create the exception response factory
         * ----------------------------------------------------------
         *
         * Register any custom exception response factories, keyed by exception type
         */
        $exceptionResponseFactoryRegistry = new ExceptionResponseFactoryRegistry();
        $exceptionResponseFactoryRegistry->registerManyFactories([
            HttpException::class => function (HttpException $ex, IHttpRequestMessage $request) {
                return $ex->getResponse();
            }
        ]);
        $exceptionResponseFactory = new ExceptionResponseFactory(
            $container->resolve(INegotiatedResponseFactory::class),
            $exceptionResponseFactoryRegistry
        );

        /**
         * ----------------------------------------------------------
         * Custom log levels
         * ----------------------------------------------------------
         *
         * Specify the exception types to log levels
         */
        $exceptionLogLevelFactories = new ExceptionLogLevelFactoryRegistry();
        $exceptionLogLevelFactories->registerManyFactories([
            HttpException::class => function (HttpException $ex) {
                if ($ex->getResponse()->getStatusCode() >= 500) {
                    return LogLevel::CRITICAL;
                }

                return LogLevel::ERROR;
            }
        ]);

        /**
         * ----------------------------------------------------------
         * Logged exception levels
         * ----------------------------------------------------------
         *
         * Specify the PSR-3 exception levels to log
         */
        $exceptionLogLevels = [LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY];

        /**
         * ----------------------------------------------------------
         * Logged error levels
         * ----------------------------------------------------------
         *
         * Specify the bitwise value of error levels to log
         */
        $errorLogLevels = 0;

        /**
         * ----------------------------------------------------------
         * Thrown error levels
         * ----------------------------------------------------------
         *
         * Specify the error levels to rethrow as exceptions
         */
        $errorThrownLevels = (E_ALL & ~(E_DEPRECATED | E_USER_DEPRECATED));

        if (!$container->hasBinding(LoggerInterface::class)) {
            (new LoggerBootstrapper)->registerBindings($container);
        }

        /**
         * ----------------------------------------------------------
         * Create the application exception handler
         * ----------------------------------------------------------
         */
        $exceptionHandler = new ExceptionHandler(
            $exceptionResponseFactory,
            $container->resolve(LoggerInterface::class),
            $exceptionLogLevelFactories,
            $exceptionLogLevels,
            $errorLogLevels,
            $errorThrownLevels,
            new StreamResponseWriter()
        );
        $exceptionHandler->registerWithPhp();
        $container->bindInstance(IExceptionHandler::class, $exceptionHandler);
    }
}