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
use Aphiria\Validation\Constraints\AggregateObjectConstraintRegistrant;
use Aphiria\Validation\Constraints\Annotations\AnnotationObjectConstraintRegistrant;
use Aphiria\Validation\Constraints\Caching\CachedObjectConstraintRegistrant;
use Aphiria\Validation\Constraints\Caching\FileObjectConstraintRegistryCache;
use Aphiria\Validation\Constraints\ObjectConstraintRegistry;
use Aphiria\Validation\ErrorMessages\IErrorMessageCompiler;
use Aphiria\Validation\ErrorMessages\StringReplaceErrorMessageCompiler;
use Aphiria\Validation\IValidator;
use Aphiria\Validation\Validator;

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
        $objectConstraints = new ObjectConstraintRegistry();
        $container->bindInstance(ObjectConstraintRegistry::class, $objectConstraints);
        $validator = new Validator($objectConstraints);
        $container->bindInstance([IValidator::class, Validator::class], $validator);

        if (getenv('APP_ENV') === 'production') {
            $constraintCache = new FileObjectConstraintRegistryCache(__DIR__ . '/../../../tmp/framework/http/validationCache.txt');
            $constraintRegistrant = new CachedObjectConstraintRegistrant($constraintCache);
            $container->bindInstance([AggregateObjectConstraintRegistrant::class, CachedObjectConstraintRegistrant::class], $constraintRegistrant);
        } else {
            $constraintRegistrant = new AggregateObjectConstraintRegistrant();
            $container->bindInstance(AggregateObjectConstraintRegistrant::class, $constraintRegistrant);
        }

        $container->bindInstance(IErrorMessageCompiler::class, new StringReplaceErrorMessageCompiler());

        // Register some constraint annotation dependencies
        $constraintAnnotationRegistrant = new AnnotationObjectConstraintRegistrant(__DIR__ . '/../..');
        $container->bindInstance(AnnotationObjectConstraintRegistrant::class, $constraintAnnotationRegistrant);
    }
}
