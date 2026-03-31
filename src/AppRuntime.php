<?php
declare(strict_types=1);

namespace Cabnet;

use Cabnet\Bootstrap\Kernel;

final class AppRuntime
{
    private Kernel $kernel;

    public function __construct(
        private object $legacyApp,
        array $routes,
        array $middlewareAliases = []
    ) {
        $this->kernel = new Kernel(
            $this->normalizeConfig($middlewareAliases),
            $this->normalizeRoutes($routes),
            $this->legacyApp
        );
        $this->kernel->boot();
    }

    public function run(): void
    {
        $this->kernel->run();
    }

    public function namedRoutes(): array
    {
        return $this->kernel->namedRoutes();
    }

    private function normalizeConfig(array $middlewareAliases): array
    {
        $config = [];

        if (method_exists($this->legacyApp, 'config')) {
            $config = [
                'app' => [
                    'debug' => (bool)$this->legacyApp->config('app.debug', false),
                ],
                'logging' => [
                    'channels' => [
                        'file' => [
                            'path' => (string)$this->legacyApp->config('logging.channels.file.path', BASE_PATH . '/storage/logs/app.log'),
                            'level' => (string)$this->legacyApp->config('logging.channels.file.level', 'error'),
                        ],
                    ],
                ],
            ];
        }

        if ($middlewareAliases !== []) {
            $config['middleware']['aliases'] = $middlewareAliases;
        } elseif (method_exists($this->legacyApp, 'config')) {
            $aliases = $this->legacyApp->config('middleware.aliases', []);
            if (is_array($aliases)) {
                $config['middleware']['aliases'] = $aliases;
            }
        }

        return $config;
    }

    private function normalizeRoutes(array $routes): array
    {
        $context = method_exists($this->legacyApp, 'context') ? (string)$this->legacyApp->context() : 'public';

        if (isset($routes[$context]) && is_array($routes[$context])) {
            return $routes;
        }

        return [$context => $routes];
    }
}
