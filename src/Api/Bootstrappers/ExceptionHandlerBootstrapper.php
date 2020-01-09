<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Api\Bootstrappers;

use Aphiria\Api\Errors\ProblemDetailsResponseMutator;
use Aphiria\Api\Validation\InvalidRequestBodyException;
use Aphiria\Api\Validation\ValidationProblemDetails;
use Aphiria\DependencyInjection\Bootstrappers\Bootstrapper;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Exceptions\ExceptionLogger;
use Aphiria\Exceptions\ExceptionLogLevelFactoryRegistry;
use Aphiria\Exceptions\ExceptionResponseFactory;
use Aphiria\Exceptions\ExceptionResponseFactoryRegistry;
use Aphiria\Exceptions\GlobalExceptionHandler;
use Aphiria\Exceptions\IExceptionLogger;
use Aphiria\Exceptions\IExceptionResponseFactory;
use Aphiria\Net\Http\ContentNegotiation\INegotiatedResponseFactory;
use Aphiria\Net\Http\HttpException;
use Aphiria\Net\Http\HttpStatusCodes;
use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\StreamResponseWriter;
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
         * Register exception response factories for common exceptions
         */
        $exceptionResponseFactoryRegistry = new ExceptionResponseFactoryRegistry();
        $container->bindInstance(ExceptionResponseFactoryRegistry::class, $exceptionResponseFactoryRegistry);
        $exceptionResponseFactoryRegistry->registerManyFactories([
            HttpException::class => fn (HttpException $ex, IHttpRequestMessage $request) => $ex->getResponse(),
            InvalidRequestBodyException::class => function (InvalidRequestBodyException $ex, IHttpRequestMessage $request, INegotiatedResponseFactory $responseFactory) {
                $body = new ValidationProblemDetails($ex->getErrors());
                $response = $responseFactory->createResponse($request, HttpStatusCodes::HTTP_BAD_REQUEST, null, $body);

                return (new ProblemDetailsResponseMutator)->mutateResponse($response);
            }
        ]);
        $exceptionResponseFactory = new ExceptionResponseFactory(
            $container->resolve(INegotiatedResponseFactory::class),
            $exceptionResponseFactoryRegistry
        );
        $container->bindInstance(IExceptionResponseFactory::class, $exceptionResponseFactory);

        /**
         * ----------------------------------------------------------
         * Custom log levels
         * ----------------------------------------------------------
         *
         * Specify the exception types to log levels for any common exceptions
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

        /**
         * ----------------------------------------------------------
         * Create the exception logger
         * ----------------------------------------------------------
         */
        $exceptionLogger = new ExceptionLogger(
            $container->resolve(LoggerInterface::class),
            $exceptionLogLevelFactories,
            $exceptionLogLevels,
            $errorLogLevels
        );
        $container->bindInstance(IExceptionLogger::class, $exceptionLogger);

        /**
         * ----------------------------------------------------------
         * Create the global exception handler
         * ----------------------------------------------------------
         */
        $globalExceptionHandler = new GlobalExceptionHandler(
            $exceptionResponseFactory,
            $exceptionLogger,
            $errorThrownLevels,
            new StreamResponseWriter()
        );
        $container->bindInstance(GlobalExceptionHandler::class, $globalExceptionHandler);
    }
}
