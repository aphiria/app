<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Aphiria\DependencyInjection\IContainer;
use Aphiria\Framework\Api\Builders\ApiApplicationBuilder;
use Aphiria\Framework\Api\Testing\PhpUnit\IntegrationTestCase as BaseIntegrationTestCase;
use Aphiria\Net\Http\IRequestHandler;
use App\GlobalModule;

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
        $globalModule = new GlobalModule($container);
        $globalModule->bootstrap();

        return (new ApiApplicationBuilder($container))->withModule($globalModule)
            ->build();
    }
}
