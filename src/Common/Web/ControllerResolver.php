<?php
declare(strict_types=1);

namespace Common\Web;

use Assert\Assertion;
use RuntimeException;
use Symfony\Component\ErrorHandler\Debug;

/**
 * Simple but convenient controller resolver.
 */
final class ControllerResolver
{
    /**
     * When the request URI is "/someRoute/", the resolver looks for a method "someRouteController" on the provided application object.
     * If it can't be found, a generic 404 controller will be returned.
     *
     * @param array $server Server parameters (simply provide `$_SERVER` as an argument)
     * @param array $get Query parameters (simply provide `$_GET` as an argument)
     * @param object $application An object containing "[route]Controller" methods
     * @return callable The controller, should be called without any arguments
     */
    public static function resolve(array $server, array $get, $application): callable
    {
        if (!isset($server['HTTP_X_INTERNAL_REQUEST'])) {
            Debug::enable();
        }

        Assertion::isObject($application, '$application should be an object containing public "[route]Controller" methods.');

        $action = trim(self::determinePathInfo($server), '/');
        $controllerMethod = [$application, ($action ?: 'index') . 'Controller'];

        if (!is_callable($controllerMethod)) {
            return self::create404Controller($application);
        }

        return function () use ($application, $controllerMethod, $get) {
            try {
                ob_start();

                if (method_exists($application, 'bootstrap')) {
                    $application->bootstrap();
                }

                $result = call_user_func_array($controllerMethod, $get);
                ob_end_flush();

                return $result;
            } catch (\Throwable $throwable) {
                ob_end_clean();

                http_response_code(500);
                header('Content-Type: plain/text');

                throw $throwable;
            }
        };
    }

    private static function create404Controller(object $application): callable
    {
        return function () use ($application) {
            error_log('ControllerResolver: No matching controller method, create 404 response');
            if (PHP_SAPI !== 'cli') {
                header('Content-Type: text/plain', true, 404);
            }
            echo "Page not found\n";

            $controllerMethods = array_filter(get_class_methods($application), function (string $methodName) {
                return substr($methodName, -10) === 'Controller';
            });

            $uris = array_map(function(string $methodName) {
                return '/' . substr($methodName, 0, -10);
            }, $controllerMethods);

            if (!empty($uris)) {
                echo "\nYou could try:\n";
                foreach ($uris as $uri) {
                    echo "- $uri\n";
                }
            }
        };
    }

    /**
     * @param array $server
     * @return mixed
     */
    private static function determinePathInfo(array $server)
    {
        if (isset($server['PATH_INFO'])) {
            return $server['PATH_INFO'];
        }

        // works for PHP-FPM
        if (isset($server['REQUEST_URI'])) {

            $requestUri = $server['REQUEST_URI'];
            if (empty($requestUri)) {
                return '/';
            }

            if ($pos = strpos($requestUri, '?')) {
                // return the request URI without query parameters
                return substr($requestUri, 0, $pos);
            }

            // the request URI doesn't contain any query parameters, return as is
            return $requestUri;
        }

        throw new RuntimeException('Could not determine path info (based on either PATH_INFO or REQUEST_URI)');
    }
}
