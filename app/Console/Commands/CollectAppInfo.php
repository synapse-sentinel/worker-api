<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ReflectionClass;
use ReflectionMethod;
use Route;

class CollectAppInfo extends Command
{
    protected $signature = 'app:collect-info';

    protected $description = 'Collects detailed application information for AI training';

    public function handle()
    {
        $routes = collect(Route::getRoutes())->map(function ($route) {
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

        $info = [
            'routes' => $routes,
            'config' => config()->all(), // Be cautious with sensitive data
        ];

        $json = json_encode($info, JSON_PRETTY_PRINT);
        file_put_contents(storage_path('app_info.json'), $json);

        $this->info('Application information collected successfully!');
    }

    private function getControllerMethods($action)
    {
        if (strpos($action, '@') !== false) {
            [$controller, $method] = explode('@', $action);
            try {
                $reflector = new ReflectionClass($controller);

                return collect($reflector->getMethods(ReflectionMethod::IS_PUBLIC))
                    ->reject(function ($method) {
                        return $method->isStatic();
                    })
                    ->map->getName()
                    ->unique()
                    ->values()
                    ->all();
            } catch (\ReflectionException $e) {
                $this->error('Error reflecting controller: '.$controller);

                return [];
            }
        }

        return [];
    }
}
