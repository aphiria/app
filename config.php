<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

use Aphiria\Net\Http\ContentNegotiation\AcceptCharsetEncodingMatcher;
use Aphiria\Net\Http\ContentNegotiation\AcceptLanguageMatcher;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\FormUrlEncodedMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\HtmlMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\JsonMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\PlainTextMediaTypeFormatter;
use Aphiria\Serialization\Encoding\CamelCasePropertyNameFormatter;
use Aphiria\Serialization\FormUrlEncodedSerializer;
use Aphiria\Serialization\JsonSerializer;
use Aphiria\Validation\ErrorMessages\StringReplaceErrorMessageInterpolater;
use DateTime;
use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;

return [
    'console' => [
        'annotationPaths' => [__DIR__ . '/src'],
        'commandCachePath' => __DIR__ . '/tmp/framework/console/commandCache.txt'
    ],
    'contentNegotiation' => [
        'encodingMatcher' => AcceptCharsetEncodingMatcher::class,
        'languageMatcher' => AcceptLanguageMatcher::class,
        'mediaTypeFormatters' => [
            JsonMediaTypeFormatter::class,
            FormUrlEncodedMediaTypeFormatter::class,
            HtmlMediaTypeFormatter::class,
            PlainTextMediaTypeFormatter::class
        ],
        'supportedLanguages' => ['en']
    ],
    'exceptions' => [
        'errorLogLevels' => 0,
        'errorThrownLevels' => (E_ALL & ~(E_DEPRECATED | E_USER_DEPRECATED)),
        'exceptionLogLevels' => [LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY],
        'useProblemDetails' => true
    ],
    'logging' => [
        'handlers' => [
            [
                'type' => StreamHandler::class,
                'path' => __DIR__ . '/tmp/logs/errors.txt'
            ]
        ],
        'name' => 'app'
    ],
    'routing' => [
        'annotationPaths' => [__DIR__ . '/src'],
        'routeCachePath' => __DIR__ . '/tmp/framework/http/routeCache.txt',
        'trieCachePath' => __DIR__ . '/tmp/framework/http/trieCache.txt'
    ],
    'serialization' => [
        'dateFormat' => DateTime::ATOM,
        'propertyNameFormatter' => CamelCasePropertyNameFormatter::class,
        'serializers' => [
            JsonSerializer::class,
            FormUrlEncodedSerializer::class
        ]
    ],
    'validation' => [
        'annotationPaths' => [__DIR__ . '/src'],
        'constraintsCachePath' => __DIR__ . '/tmp/framework/constraints.txt',
        'errorMessageInterpolator' => [
            'type' => StringReplaceErrorMessageInterpolater::class
        ]
    ]
];
