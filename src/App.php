<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2020 David Young
 * @license   https://github.com/aphiria/app/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace App;

use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\Application\IModule;
use Aphiria\Configuration\GlobalConfiguration;
use Aphiria\Configuration\MissingConfigurationValueException;
use Aphiria\DependencyInjection\Binders\IBinderDispatcher;
use Aphiria\DependencyInjection\Binders\LazyBinderDispatcher;
use Aphiria\DependencyInjection\Binders\Metadata\Caching\FileBinderMetadataCollectionCache;
use Aphiria\DependencyInjection\Binders\Metadata\Caching\IBinderMetadataCollectionCache;
use Aphiria\DependencyInjection\Container;
use Aphiria\Framework\Api\Binders\ControllerBinder;
use Aphiria\Framework\Application\AphiriaComponents;
use Aphiria\Framework\Console\Binders\CommandBinder;
use Aphiria\Framework\Exceptions\Binders\ExceptionHandlerBinder;
use Aphiria\Framework\Net\Binders\ContentNegotiationBinder;
use Aphiria\Framework\Net\Binders\RequestBinder;
use Aphiria\Framework\Routing\Binders\RoutingBinder;
use Aphiria\Framework\Serialization\Binders\SerializerBinder;
use Aphiria\Framework\Validation\Binders\ValidationBinder;
use Aphiria\Net\Http\HttpException;
use App\Demo\UserModule;
use Exception;
use Psr\Log\LogLevel;

/**
 * Defines the application
 */
final class App implements IModule
{
    use AphiriaComponents;

    /**
     * Configures the application's modules and components
     *
     * @param IApplicationBuilder $appBuilder The builder that will build our app
     * @throws Exception Thrown if there was an error building the app
     */
    public function build(IApplicationBuilder $appBuilder): void
    {
        // Configure the app
        $this->withBinderDispatcher($appBuilder, $this->getBinderDispatcher())
            ->withRouteAnnotations($appBuilder)
            ->withValidatorAnnotations($appBuilder)
            ->withCommandAnnotations($appBuilder)
            ->withBinders($appBuilder, [
                new ExceptionHandlerBinder(),
                new RequestBinder(),
                new SerializerBinder(),
                new ValidationBinder(),
                new ContentNegotiationBinder(),
                new ControllerBinder(),
                new RoutingBinder(),
                new CommandBinder()
            ])
            ->withLogLevelFactory($appBuilder, HttpException::class, static function (HttpException $ex) {
                return $ex->getResponse()->getStatusCode() >= 500 ? LogLevel::ERROR : LogLevel::DEBUG;
            })
            ->withModules($appBuilder, [
                new UserModule()
            ]);
    }

    /**
     * Gets the binder dispatcher
     *
     * @return IBinderDispatcher The binder dispatcher to use
     * @throws MissingConfigurationValueException Thrown if the path to the metadata cache was missing
     */
    private function getBinderDispatcher(): IBinderDispatcher
    {
        // Always bind the cache so that we have the option to clear it in any environment
        $cachePath = GlobalConfiguration::getString('aphiria.binders.metadataCachePath');
        $cache = new FileBinderMetadataCollectionCache($cachePath);
        Container::$globalInstance->bindInstance(IBinderMetadataCollectionCache::class, $cache);

        if (\getenv('APP_ENV') === 'production') {
            return new LazyBinderDispatcher($cache);
        }

        return new LazyBinderDispatcher();
    }
}
