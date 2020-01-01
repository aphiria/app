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
use Aphiria\Serialization\Encoding\CamelCasePropertyNameFormatter;
use Aphiria\Serialization\Encoding\DefaultEncoderRegistrant;
use Aphiria\Serialization\Encoding\EncoderRegistry;
use Aphiria\Serialization\FormUrlEncodedSerializer;
use Aphiria\Serialization\JsonSerializer;
use DateTime;

/**
 * Defines the bootstrapper for serializers
 */
final class SerializerBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $encoders = new EncoderRegistry();

        /**
         * ----------------------------------------------------------
         * Define your default encoder settings
         * ----------------------------------------------------------
         *
         * Note:  If you'd prefer to not format your property names,
         * you can set the property name formatter to null.
         */
        $propertyNameFormatter = new CamelCasePropertyNameFormatter();
        (new DefaultEncoderRegistrant($propertyNameFormatter, DateTime::ATOM))->registerDefaultEncoders($encoders);

        $container->bindInstance(EncoderRegistry::class, $encoders);
        $container->bindInstance(JsonSerializer::class, new JsonSerializer($encoders));
        $container->bindInstance(FormUrlEncodedSerializer::class, new FormUrlEncodedSerializer($encoders));
    }
}
