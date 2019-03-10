<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

use Aphiria\Net\Http\ContentNegotiation\ContentNegotiator;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\FormUrlEncodedSerializerMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\HtmlMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\JsonMediaTypeFormatter;
use Aphiria\Net\Http\ContentNegotiation\MediaTypeFormatters\PlainTextMediaTypeFormatter;

/**
 * ----------------------------------------------------------
 * Media type formatters
 * ----------------------------------------------------------
 *
 * Specify the media type formatters you support
 * Note: The first registered media type formatter will be considered the default one
 */
$mediaTypeFormatters = [
    new JsonMediaTypeFormatter(),
    new FormUrlEncodedSerializerMediaTypeFormatter(),
    new HtmlMediaTypeFormatter(),
    new PlainTextMediaTypeFormatter()
];

/**
 * ----------------------------------------------------------
 * Supported languages
 * ----------------------------------------------------------
 *
 * Specify the language tags you support
 * @link https://tools.ietf.org/html/rfc5646
 */
$supportedLanguages = [
    'en-US'
];

return new ContentNegotiator($mediaTypeFormatters, $supportedLanguages);
