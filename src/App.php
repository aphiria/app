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
use Aphiria\Application\IModule;
use Aphiria\DependencyInjection\Binders\IBinderDispatcher;
use Aphiria\DependencyInjection\Binders\LazyBinderDispatcher;
use Aphiria\DependencyInjection\Binders\Metadata\Caching\FileBinderMetadataCollectionCache;
use Aphiria\DependencyInjection\Binders\Metadata\Caching\IBinderMetadataCollectionCache;
use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Aphiria\Framework\Api\Binders\ControllerBinder;
use Aphiria\Framework\Api\Exceptions\ExceptionHandler;
use Aphiria\Framework\Application\AphiriaComponents;
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
     * @param IContainer $container The application's DI container
     */
    public function __construct(IContainer $container)
    {
        // Bootstrap the application
        $globalConfigurationBuilder = (new GlobalConfigurationBuilder())->withEnvironmentVariables()
            ->withPhpFileConfigurationSource(__DIR__ . '/../config.php');
        (new BootstrapperCollection())->addMany([
            new DotEnvBootstrapper(__DIR__ . '/../.env'),
            new ConfigurationBootstrapper($globalConfigurationBuilder),
            new GlobalExceptionHandlerBootstrapper($container)
        ])->bootstrapAll();
    }

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
            ->withFrameworkCommands($appBuilder)
            ->withRouteAnnotations($appBuilder)
            ->withValidatorAnnotations($appBuilder)
            ->withCommandAnnotations($appBuilder)
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
