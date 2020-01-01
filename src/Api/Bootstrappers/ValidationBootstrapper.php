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
use Aphiria\Validation\Constraints\Annotations\AnnotationObjectConstraintsRegistrant;
use Aphiria\Validation\Constraints\Caching\CachedObjectConstraintsRegistrant;
use Aphiria\Validation\Constraints\Caching\FileObjectConstraintsRegistryCache;
use Aphiria\Validation\Constraints\ObjectConstraintsRegistrantCollection;
use Aphiria\Validation\Constraints\ObjectConstraintsRegistry;
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
        $objectConstraints = new ObjectConstraintsRegistry();
        $container->bindInstance(ObjectConstraintsRegistry::class, $objectConstraints);
        $validator = new Validator($objectConstraints);
        $container->bindInstance([IValidator::class, Validator::class], $validator);
        $constraintsRegistrants = new ObjectConstraintsRegistrantCollection();
        $container->bindInstance(ObjectConstraintsRegistrantCollection::class, $constraintsRegistrants);

        if (getenv('APP_ENV') === 'production') {
            $constraintCache = new FileObjectConstraintsRegistryCache(__DIR__ . '/../../../tmp/framework/http/validationCache.txt');
            $constraintRegistrant = new CachedObjectConstraintsRegistrant($constraintCache, $constraintsRegistrants);
            $container->bindInstance(CachedObjectConstraintsRegistrant::class, $constraintRegistrant);
        }

        $container->bindInstance(IErrorMessageCompiler::class, new StringReplaceErrorMessageCompiler());

        // Register some constraint annotation dependencies
        $constraintAnnotationRegistrant = new AnnotationObjectConstraintsRegistrant(__DIR__ . '/../..');
        $container->bindInstance(AnnotationObjectConstraintsRegistrant::class, $constraintAnnotationRegistrant);
    }
}
