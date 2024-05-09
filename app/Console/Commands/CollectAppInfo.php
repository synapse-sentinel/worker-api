<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;
use ReflectionMethod;
use Route;

class CollectAppInfo extends Command
{
    protected $signature = 'app:collect-info';

    protected $description = 'Collects detailed application information for AI training, ensuring data sensitivity and performance optimization';

    public function handle()
    {
        $routes = $this->collectRoutes();
        $config = $this->filterSensitiveConfig();

        $info = [
            'routes' => $routes,
            'config' => $config,
        ];

        $json = json_encode($info, JSON_PRETTY_PRINT);
        file_put_contents(storage_path('app_info.json'), $json);

        $this->info('Application information collected successfully!');
    }

    protected function collectRoutes()
    {
        return collect(Route::getRoutes())->map(function ($route) {
            $action = $route->getActionName();

            return [
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $action,
                'middleware' => $route->middleware(),
                'parameters' => $route->parameterNames(),
                'controller_methods' => $this->getControllerMethods($action),
            ];
        });
    }

    protected function filterSensitiveConfig()
    {
        $allConfig = config()->all();
        $sensitiveKeys = ['db_password', 'api_key', 'secret']; // Define your sensitive keys here
        array_walk_recursive($allConfig, function (&$value, $key) use ($sensitiveKeys) {
            if (in_array($key, $sensitiveKeys)) {
                $value = '********'; // Obscure sensitive values
            }
        });

        return $allConfig;
    }

    protected function getControllerMethods($action)
    {
        if (strpos($action, '@') !== false) {
            [$controller, $method] = explode('@', $action);
            $cacheKey = "controller_methods_{$controller}";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            try {
                $reflector = new ReflectionClass($controller);
                $methods = collect($reflector->getMethods(ReflectionMethod::IS_PUBLIC))
                    ->reject(function ($method) {
                        return $method->isStatic();
                    })
                    ->map->getName()
                    ->unique()
                    ->values()
                    ->all();
                Cache::put($cacheKey, $methods, 3600); // Cache for 1 hour

                return $methods;
            } catch (\ReflectionException $e) {
                $this->error('Error reflecting controller: '.$controller);

                return [];
            }
        }

        return [];
    }
}
