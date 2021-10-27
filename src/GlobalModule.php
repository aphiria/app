<?php

declare(strict_types=1);

namespace App;

use Aphiria\Application\BootstrapperCollection;
use Aphiria\Application\Builders\IApplicationBuilder;
use Aphiria\Application\Configuration\Bootstrappers\ConfigurationBootstrapper;
use Aphiria\Application\Configuration\Bootstrappers\DotEnvBootstrapper;
use Aphiria\Application\Configuration\GlobalConfiguration;
use Aphiria\Application\Configuration\GlobalConfigurationBuilder;
use Aphiria\Application\Configuration\MissingConfigurationValueException;
use Aphiria\Application\IBootstrapper;
use Aphiria\DependencyInjection\Binders\IBinderDispatcher;
use Aphiria\DependencyInjection\Binders\LazyBinderDispatcher;
use Aphiria\DependencyInjection\Binders\Metadata\Caching\FileBinderMetadataCollectionCache;
use Aphiria\DependencyInjection\Binders\Metadata\Caching\IBinderMetadataCollectionCache;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Framework\Api\Binders\ControllerBinder;
use Aphiria\Framework\Api\Exceptions\ExceptionHandler;
use Aphiria\Framework\Application\AphiriaModule;
use Aphiria\Framework\Console\Binders\CommandBinder;
use Aphiria\Framework\ContentNegotiation\Binders\ContentNegotiationBinder;
use Aphiria\Framework\Exceptions\Binders\ExceptionHandlerBinder;
use Aphiria\Framework\Exceptions\Bootstrappers\GlobalExceptionHandlerBootstrapper;
use Aphiria\Framework\Net\Binders\RequestBinder;
use Aphiria\Framework\Routing\Binders\RoutingBinder;
use Aphiria\Framework\Serialization\Binders\SymfonySerializerBinder;
use Aphiria\Framework\Validation\Binders\ValidationBinder;
use Aphiria\Middleware\MiddlewareBinding;
use Aphiria\Net\Http\HttpException;
use App\Demo\DemoModule;
use Exception;
use Psr\Log\LogLevel;

/**
 * Defines the global module for our application
 */
final class GlobalModule extends AphiriaModule implements IBootstrapper
{
    /**
     * @param IContainer $container The application's DI container
     */
    public function __construct(private IContainer $container)
    {
    }

    /**
     * Bootstraps our application, which is the first thing done when starting an application
     */
    public function bootstrap(): void
    {
        $globalConfigurationBuilder = (new GlobalConfigurationBuilder())->withEnvironmentVariables()
            ->withPhpFileConfigurationSource(__DIR__ . '/../config.php');
        (new BootstrapperCollection())->addMany([
            new DotEnvBootstrapper(__DIR__ . '/../.env'),
            new ConfigurationBootstrapper($globalConfigurationBuilder),
            new GlobalExceptionHandlerBootstrapper($this->container)
        ])->bootstrapAll();
    }

    /**
     * Configures the application's modules and components
     *
     * @param IApplicationBuilder $appBuilder The builder that will build our app
     * @throws Exception Thrown if there was an error building the app
     */
    public function configure(IApplicationBuilder $appBuilder): void
    {
        $this->withBinderDispatcher($appBuilder, $this->getBinderDispatcher())
            ->withFrameworkCommands($appBuilder)
            ->withRouteAttributes($appBuilder)
            ->withValidatorAttributes($appBuilder)
            ->withCommandAttributes($appBuilder)
            ->withGlobalMiddleware($appBuilder, [
                new MiddlewareBinding(ExceptionHandler::class)
            ])
            ->withBinders($appBuilder, [
                new ExceptionHandlerBinder(),
                new RequestBinder(),
                new SymfonySerializerBinder(),
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
                new DemoModule()
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
        $this->container->bindInstance(IBinderMetadataCollectionCache::class, $cache);

        if (\getenv('APP_ENV') === 'production') {
            return new LazyBinderDispatcher($cache);
        }

        return new LazyBinderDispatcher();
    }
}
