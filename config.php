<?php

declare(strict_types=1);

use Aphiria\ContentNegotiation\AcceptCharsetEncodingMatcher;
use Aphiria\ContentNegotiation\AcceptLanguageMatcher;
use Aphiria\ContentNegotiation\MediaTypeFormatters\HtmlMediaTypeFormatter;
use Aphiria\ContentNegotiation\MediaTypeFormatters\JsonMediaTypeFormatter;
use Aphiria\ContentNegotiation\MediaTypeFormatters\PlainTextMediaTypeFormatter;
use Aphiria\ContentNegotiation\MediaTypeFormatters\XmlMediaTypeFormatter;
use Aphiria\Framework\Api\Exceptions\ProblemDetailsExceptionRenderer;
use Aphiria\Framework\Serialization\Normalizers\ProblemDetailsNormalizer;
use Aphiria\Validation\ErrorMessages\DefaultErrorMessageTemplateRegistry;
use Aphiria\Validation\ErrorMessages\StringReplaceErrorMessageInterpolator;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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
         * Configure the API
         * ----------------------------------------------------------
         *
         * localhostRouterPath => The path to the localhost router file
         */
        'api' => [
            'localhostRouterPath' => __DIR__ . '/localhost_router.php'
        ],

        /**
         * ----------------------------------------------------------
         * Configure authorization
         * ----------------------------------------------------------
         *
         * continueOnFailure => Whether or not to continue requirement checks on failure
         */
        'authorization' => [
            'continueOnFailure' => true
        ],

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
         * attributePaths => The paths to search for command attributes
         * commandCachePath => The path to the compiled commands
         */
        'console' => [
            'attributePaths' => [__DIR__ . '/src'],
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
                XmlMediaTypeFormatter::class,
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
         * apiExceptionRenderer => The API exception renderer to use for API applications
         */
        'exceptions' => [
            'apiExceptionRenderer' => ProblemDetailsExceptionRenderer::class
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
                    'level' => \getenv('LOG_LEVEL')
                ]
            ],
            'name' => 'app'
        ],

        /**
         * ----------------------------------------------------------
         * Configure the router
         * ----------------------------------------------------------
         *
         * attributePaths => The paths to search for route attributes
         * routeCachePath => The path to the route cache
         * trieCachePath => The path to the trie cache
         */
        'routing' => [
            'attributePaths' => [__DIR__ . '/src'],
            'routeCachePath' => __DIR__ . '/tmp/framework/api/routeCache.txt',
            'trieCachePath' => __DIR__ . '/tmp/framework/api/trieCache.txt'
        ],

        /**
         * ----------------------------------------------------------
         * Configure the Symfony serializer
         * ----------------------------------------------------------
         *
         * dateFormat => The format to use for all dates
         * encoders => The list of encoders the serializer will use
         * nameConverter => The property name converter (can be null)
         * normalizers => The list of normalizers the serializer will use
         * xml.removeEmptyTags => Whether or not to remove empty XML tags
         * xml.rootNodeName => The name of the default XML root node
         */
        'serialization' => [
            'dateFormat' => DateTime::ATOM,
            'encoders' => [
                JsonEncoder::class,
                XmlEncoder::class
            ],
            'nameConverter' => null,
            'normalizers' => [
                DateTimeNormalizer::class,
                BackedEnumNormalizer::class,
                ProblemDetailsNormalizer::class,
                ObjectNormalizer::class,
                ArrayDenormalizer::class
            ],
            'xml' => [
                'removeEmptyTags' => false,
                'rootNodeName' => 'response'
            ]
        ],

        /**
         * ----------------------------------------------------------
         * Configure the validator
         * ----------------------------------------------------------
         *
         * attributePaths => The paths to search for constraint attributes
         * constraintsCachePath => The path to the constraint cache
         * errorMessageInterpolator => The error message interpolator to use
         * errorMessageTemplates => The error message template registry to use
         */
        'validation' => [
            'attributePaths' => [__DIR__ . '/src'],
            'constraintsCachePath' => __DIR__ . '/tmp/framework/constraintsCache.txt',
            'errorMessageInterpolator' => [
                'type' => StringReplaceErrorMessageInterpolator::class
            ],
            'errorMessageTemplates' => [
                'type' => DefaultErrorMessageTemplateRegistry::class
            ]
        ]
    ]
];
