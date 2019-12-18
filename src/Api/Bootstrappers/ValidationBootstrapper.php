<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Api\Bootstrappers;

use Aphiria\DependencyInjection\Bootstrappers\Bootstrapper;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Validation\AggregateConstraintRegistrant;
use Aphiria\Validation\Caching\CachedConstraintRegistrant;
use Aphiria\Validation\Caching\FileConstraintRegistryCache;
use Aphiria\Validation\ConstraintRegistry;
use Aphiria\ValidationAnnotations\AnnotationConstraintRegistrant;

/**
 * Defines the bootstrapper for model validation
 */
final class ValidationBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $constraints = new ConstraintRegistry();
        $container->bindInstance(ConstraintRegistry::class, $constraints);

        if (getenv('APP_ENV') === 'production') {
            $constraintCache = new FileConstraintRegistryCache(__DIR__ . '/../../../tmp/framework/http/validationCache.txt');
            $constraintRegistrant = new CachedConstraintRegistrant($constraintCache);
            $container->bindInstance([AggregateConstraintRegistrant::class, CachedConstraintRegistrant::class], $constraintRegistrant);
        } else {
            $constraintRegistrant = new AggregateConstraintRegistrant();
            $container->bindInstance(AggregateConstraintRegistrant::class, $constraintRegistrant);
        }

        // Register some constraint annotation dependencies
        $constraintAnnotationRegistrant = new AnnotationConstraintRegistrant(__DIR__ . '/../..');
        $container->bindInstance(AnnotationConstraintRegistrant::class, $constraintAnnotationRegistrant);
    }
}
