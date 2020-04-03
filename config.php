<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

use Aphiria\Net\Http\ContentNegotiation\AcceptCharsetEncodingMatcher;
use Aphiria\Net\Http\ContentNegotiation\AcceptLanguageMatcher;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\FormUrlEncodedMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\HtmlMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\JsonMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\PlainTextMediaTypeFormatter;
use Aphiria\Serialization\Encoding\CamelCasePropertyNameFormatter;
use Aphiria\Serialization\FormUrlEncodedSerializer;
use Aphiria\Serialization\JsonSerializer;
use Aphiria\Validation\ErrorMessages\DefaultErrorMessageTemplateRegistry;
use Aphiria\Validation\ErrorMessages\StringReplaceErrorMessageInterpolator;
use Monolog\Handler\StreamHandler;

return [
    /**
     * ----------------------------------------------------------
     * Configure Aphiria components
     * ----------------------------------------------------------
     *
     * Note: Removing values here could result in the application not working
     */
    'aphiria' => [
        /**
         * ----------------------------------------------------------
         * Configure the binders
         * ----------------------------------------------------------
         *
         * metadataCachePath => The path to the binder metadata cache
         */
        'binders' => [
            'metadataCachePath' => __DIR__ . '/tmp/framework/binderMetadataCollectionCache.txt'
        ],

        /**
         * ----------------------------------------------------------
         * Configure the console
         * ----------------------------------------------------------
         *
         * annotationPaths => The paths to search for command annotations
         * commandCachePath => The path to the compiled commands
         */
        'console' => [
            'annotationPaths' => [__DIR__ . '/src'],
            'commandCachePath' => __DIR__ . '/tmp/framework/console/commandCache.txt'
        ],

        /**
         * ----------------------------------------------------------
         * Configure the content negotiator
         * ----------------------------------------------------------
         *
         * encodingMatcher => The matcher for character encodings
         * languageMatcher => The matcher for languages
         * mediaTypeFormatters => The list of supported media type formatters (first one will be the default)
         * supportedLanguages => The list of languages the API supports
         */
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

        /**
         * ----------------------------------------------------------
         * Configure exception handling
         * ----------------------------------------------------------
         *
         * useProblemDetails => Whether or not to use problem detail responses
         */
        'exceptions' => [
            'useProblemDetails' => true
        ],

        /**
         * ----------------------------------------------------------
         * Configure the logger
         * ----------------------------------------------------------
         *
         * handlers => The list of handlers to use with logging
         * name => The name of the logger
         */
        'logging' => [
            'handlers' => [
                [
                    'type' => StreamHandler::class,
                    'path' => __DIR__ . '/tmp/logs/errors.txt',
                    'level' => getenv('LOG_LEVEL')
                ]
            ],
            'name' => 'app'
        ],

        /**
         * ----------------------------------------------------------
         * Configure the router
         * ----------------------------------------------------------
         *
         * annotationPaths => The paths to search for route annotations
         * routeCachePath => The path to the route cache
         * trieCachePath => The path to the trie cache
         */
        'routing' => [
            'annotationPaths' => [__DIR__ . '/src'],
            'routeCachePath' => __DIR__ . '/tmp/framework/api/routeCache.txt',
            'trieCachePath' => __DIR__ . '/tmp/framework/api/trieCache.txt'
        ],

        /**
         * ----------------------------------------------------------
         * Configure the serializer
         * ----------------------------------------------------------
         *
         * dateFormat => The format to use for all dates
         * propertyNameFormatter => The formatter to use for property names (can be null)
         * serializers => The list of supported serializers
         */
        'serialization' => [
            'dateFormat' => DateTime::ATOM,
            'propertyNameFormatter' => CamelCasePropertyNameFormatter::class,
            'serializers' => [
                JsonSerializer::class,
                FormUrlEncodedSerializer::class
            ]
        ],

        /**
         * ----------------------------------------------------------
         * Configure the validator
         * ----------------------------------------------------------
         *
         * annotationPaths => The paths to search for constraint annotations
         * constraintsCachePath => The path to the constraint cache
         * errorMessageInterpolator => The error message interpolator to use
         * errorMessageTemplates => The error message template registry to use
         */
        'validation' => [
            'annotationPaths' => [__DIR__ . '/src'],
            'constraintsCachePath' => __DIR__ . '/tmp/framework/constraints.txt',
            'errorMessageInterpolator' => [
                'type' => StringReplaceErrorMessageInterpolator::class
            ],
            'errorMessageTemplates' => [
                'type' => DefaultErrorMessageTemplateRegistry::class
            ]
        ]
    ]
];
