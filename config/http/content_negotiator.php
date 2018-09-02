<?php
/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2018 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
use Opulence\Net\Http\ContentNegotiation\ContentNegotiator;
use Opulence\Net\Http\ContentNegotiation\MediaTypeFormatters\FormUrlEncodedSerializerMediaTypeFormatter;
use Opulence\Net\Http\ContentNegotiation\MediaTypeFormatters\HtmlMediaTypeFormatter;
use Opulence\Net\Http\ContentNegotiation\MediaTypeFormatters\JsonMediaTypeFormatter;
use Opulence\Net\Http\ContentNegotiation\MediaTypeFormatters\PlainTextMediaTypeFormatter;

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
