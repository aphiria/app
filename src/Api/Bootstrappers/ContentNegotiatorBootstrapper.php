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

use Aphiria\DependencyInjection\Bootstrappers\Bootstrapper;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Net\Http\ContentNegotiation\AcceptCharsetEncodingMatcher;
use Aphiria\Net\Http\ContentNegotiation\AcceptLanguageMatcher;
use Aphiria\Net\Http\ContentNegotiation\ContentNegotiator;
use Aphiria\Net\Http\ContentNegotiation\IContentNegotiator;
use Aphiria\Net\Http\ContentNegotiation\IEncodingMatcher;
use Aphiria\Net\Http\ContentNegotiation\ILanguageMatcher;
use Aphiria\Net\Http\ContentNegotiation\IMediaTypeFormatterMatcher;
use Aphiria\Net\Http\ContentNegotiation\INegotiatedResponseFactory;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatterMatcher;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\FormUrlEncodedMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\HtmlMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\JsonMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\PlainTextMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\NegotiatedResponseFactory;
use Aphiria\Serialization\FormUrlEncodedSerializer;
use Aphiria\Serialization\JsonSerializer;

/**
 * Defines the bootstrapper that registers the content negotiator
 */
final class ContentNegotiatorBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        /**
         * ----------------------------------------------------------
         * Media type formatter matcher
         * ----------------------------------------------------------
         *
         * Configure how you want media type formatters to be matched.
         * Note: The first registered media type formatter will be considered the default one
         * Default: Use the encoding set in the Accept-Charset header
         * @link https://tools.ietf.org/html/rfc5646
         */
        $mediaTypeFormatters = [
            new JsonMediaTypeFormatter($container->resolve(JsonSerializer::class)),
            new FormUrlEncodedMediaTypeFormatter($container->resolve(FormUrlEncodedSerializer::class)),
            new HtmlMediaTypeFormatter(),
            new PlainTextMediaTypeFormatter()
        ];
        $mediaTypeFormatterMatcher = new MediaTypeFormatterMatcher($mediaTypeFormatters);
        $container->bindInstance(IMediaTypeFormatterMatcher::class, $mediaTypeFormatterMatcher);

        /**
         * ----------------------------------------------------------
         * Encoding matcher
         * ----------------------------------------------------------
         *
         * Configure how you want encodings to be matched.
         * Default: Use the encoding set in the Accept-Charset header
         * @link https://tools.ietf.org/html/rfc5646
         */
        $encodingMatcher = new AcceptCharsetEncodingMatcher();
        $container->bindInstance(IEncodingMatcher::class, $encodingMatcher);

        /**
         * ----------------------------------------------------------
         * Language matcher
         * ----------------------------------------------------------
         *
         * Configure how you want languages to be matched.  The supported languages must follow RFC 5646.
         * Default: Use the Accept-Language header
         * @link https://tools.ietf.org/html/rfc5646
         */
        $supportedLanguages = [
            'en-US'
        ];
        $languageMatcher = new AcceptLanguageMatcher($supportedLanguages);
        $container->bindInstance(ILanguageMatcher::class, $languageMatcher);

        $contentNegotiator = new ContentNegotiator(
            $mediaTypeFormatters,
            $mediaTypeFormatterMatcher,
            $encodingMatcher,
            $languageMatcher
        );
        $negotiatedResponseFactory = new NegotiatedResponseFactory($contentNegotiator);
        $container->bindInstance(IContentNegotiator::class, $contentNegotiator);
        $container->bindInstance(INegotiatedResponseFactory::class, $negotiatedResponseFactory);
    }
}
