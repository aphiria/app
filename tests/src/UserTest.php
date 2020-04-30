<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App\Tests\src;

use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\IServiceResolver;
use Aphiria\Framework\Api\Builders\ApiApplicationBuilder;
use Aphiria\Framework\Testing\ApplicationClient;
use Aphiria\Framework\Testing\RequestBuilder;
use Aphiria\Framework\Testing\ResponseAssertions;
use App\App;
use App\Demo\User;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    protected ApplicationClient $client;
    protected ResponseAssertions $responseAssertions;
    protected RequestBuilder $requestBuilder;

    protected function setUp(): void
    {
        // TODO: How do we centralize this logic?
        $autoloader = require __DIR__ . '/../../vendor/autoload.php';
        AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);
        $container = new Container();
        Container::$globalInstance = $container;
        $container->bindInstance([IServiceResolver::class, IContainer::class, Container::class], $container);
        $this->client = new ApplicationClient(
            (new ApiApplicationBuilder($container))->withModule(new App($container)),
            $container
        );

        // TODO: Do I add a test module so that I can use binders to grab things like the media type formatter matcher?
        // TODO: RequestBinder binds a localhost request when running in CLI.  How do we use the request that's actually sent via the client?
        $this->requestBuilder = new RequestBuilder();
        $this->responseAssertions = new ResponseAssertions();
        // TODO: Because I'm not resolving the request anywhere, the RequestBinder is not getting executed, which means the request isn't being set in the ApiExceptionRenderer.  How do I want this to work?  Do I need to bind the built request to the DI container?  Does RequestBinder even get run in the test environment?
    }

    public function testGettingAllUsers(): void
    {
        $request = $this->requestBuilder->withMethod('GET')
            ->withUri('http://localhost/users?letMeIn=1')
            ->build();
        $response = $this->client->send($request);
        // TODO: Should I drop the "assert" prefix (like in C#) and go with $this->responseAssertions->statusCodeEquals(200, $response);
        $this->responseAssertions->assertStatusCodeEquals(200, $response);
        $this->responseAssertions->assertParsedBodyEquals(
            [new User(192153, 'foo@bar.com')],
            $request,
            $response
        );
    }

    public function testGettingInvalidUserReturns404(): void
    {
        $request = $this->requestBuilder->withMethod('GET')
            ->withUri('http://localhost/users/-1')
            ->build();
        $response = $this->client->send($request);
        $this->responseAssertions->assertStatusCodeEquals(404, $response);
    }
}
