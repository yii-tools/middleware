<?php

declare(strict_types=1);

namespace Yii\Middleware\Tests\Support;

use HttpSoft\Message\RequestFactory;
use HttpSoft\Message\ResponseFactory;
use HttpSoft\Message\ServerRequest;
use HttpSoft\Message\ServerRequestFactory;
use HttpSoft\Message\StreamFactory;
use HttpSoft\Message\UploadedFileFactory;
use HttpSoft\Message\UriFactory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yii\Middleware\Locale;
use Yii\Middleware\Tests\Support\IdentityRepository;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Config\Config;
use Yiisoft\Config\ConfigPaths;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollectionInterface;
use Yiisoft\Router\RouteCollector;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;

trait TestTrait
{
    private RequestHandlerInterface $handler;
    private ResponseFactoryInterface $responseFactory;
    private ServerRequestInterface $request;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;

    private function createContainer(): void
    {
        $config = new Config(new ConfigPaths(dirname(__DIR__, 2), 'config', 'vendor'), 'tests');

        $containerConfig = ContainerConfig::create()
            ->withDefinitions(array_merge($config->get('di-web'), $this->createConfig()));

        $container = new Container($containerConfig);

        $this->handler = $container->get(RequestHandlerInterface::class);
        $this->request = $container->get(ServerRequestInterface::class);
        $this->responseFactory = $container->get(ResponseFactoryInterface::class);
        $this->translator = $container->get(TranslatorInterface::class);
        $this->urlGenerator = $container->get(UrlGeneratorInterface::class);
    }

    public function createConfig(): array
    {
        return [
            // Defined auth identity repository.
            IdentityRepositoryInterface::class => [
                'class' => IdentityRepository::class,
                '__construct()' => [
                    'identityClass' => IdentityRepository::class,
                ],
            ],

            // Defined PSR-7 and PSR-17 factories.
            RequestFactoryInterface::class => RequestFactory::class,
            RequestHandlerInterface::class => RequestHandler::class,
            ResponseFactoryInterface::class => ResponseFactory::class,
            ServerRequestFactoryInterface::class => ServerRequestFactory::class,
            ServerRequestInterface::class => ServerRequest::class,
            StreamFactoryInterface::class => StreamFactory::class,
            UploadedFileFactoryInterface::class => UploadedFileFactory::class,
            UriFactoryInterface::class => UriFactory::class,

            // Router configuration.
            RouteCollectionInterface::class => static function () {
                $routes = [
                    Route::get('/')->name('home'),
                    Route::get('/login')->name('login'),
                ];

                $group = Group::create()->routes(...$routes);
                $collector = new RouteCollector();
                $collector->addGroup($group);

                return new RouteCollection($collector);
            }
        ];
    }
}
