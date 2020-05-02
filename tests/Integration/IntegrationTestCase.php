<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Tests\Integration;

use Aphiria\DependencyInjection\IContainer;
use Aphiria\Framework\Api\Builders\ApiApplicationBuilder;
use Aphiria\Framework\Testing\PhpUnit\IntegrationTestCase as BaseIntegrationTestCase;
use Aphiria\Net\Http\Handlers\IRequestHandler;
use App\App;

/**
 * Defines the base integration test case
 */
class IntegrationTestCase extends BaseIntegrationTestCase
{
    /**
     * @inheritdoc
     */
    protected function createApplication(IContainer $container): IRequestHandler
    {
        return (new ApiApplicationBuilder($container))->withModule(new App($container))
            ->build();
    }
}
