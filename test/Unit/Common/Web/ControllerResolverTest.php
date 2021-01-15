<?php
declare(strict_types=1);

namespace Common\Web;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Common\Web\Fixtures\Application;

class ControllerResolverTest extends TestCase
{
    private Application $application;

    protected function setUp(): void
    {
        $this->application = new Application();
    }

    /**
     * @test
     */
    public function it_returns_the_method_that_matches_path_info_from_the_request(): void
    {
        $controller = ControllerResolver::resolve(
            ['PATH_INFO' => '/some'],
            [],
            $this->application
        );

        $this->assertEquals(
            // when called, $controller will return its own name
            'Common\Web\Fixtures\Application::someController',
            $controller()
        );
    }

    /**
     * @test
     */
    public function it_forwards_query_parameters_as_controller_method_arguments(): void
    {
        $get = ['id' => '1', 'orderId' => '123'];

        $controller = ControllerResolver::resolve(
            ['PATH_INFO' => '/withArguments'],
            $get,
            $this->application
        );

        $this->assertEquals(
            // when called, $controller will return its arguments
            array_values($get),
            $controller()
        );
    }

    /**
     * @test
     */
    public function it_fails_when_it_can_not_determine_the_path_info(): void
    {
        $this->expectException(RuntimeException::class);

        ControllerResolver::resolve(
            [],
            [],
            $this->application
        );
    }

    /**
     * @test
     */
    public function it_shows_alternative_routes(): void
    {
        $controller = ControllerResolver::resolve(
            ['PATH_INFO' => '/unknown'],
            [],
            $this->application
        );

        ob_start();
        $controller();
        $response = ob_get_contents();
        self::assertNotFalse($response);
        ob_end_clean();

        $this->assertStringContainsString('/some', $response);
        $this->assertStringContainsString('/withArguments', $response);

        echo $response;
    }

    /**
     * @test
     */
    public function it_resolves_an_empty_route_to_the_index_controller(): void
    {
        $controller = ControllerResolver::resolve(
            ['PATH_INFO' => '/'],
            [],
            $this->application
        );

        $this->assertEquals(
            // when called, $controller will return its own name
            'Common\Web\Fixtures\Application::indexController',
            $controller()
        );
    }

    /**
     * @test
     */
    public function it_uses_request_uri_if_path_info_is_not_available(): void
    {
        $controller = ControllerResolver::resolve(
            ['REQUEST_URI' => '/some'],
            [],
            $this->application
        );

        $this->assertEquals(
        // when called, $controller will return its own name
            'Common\Web\Fixtures\Application::someController',
            $controller()
        );
    }

    /**
     * @test
     */
    public function it_strips_query_parameters_from_the_request_uri(): void
    {
        $controller = ControllerResolver::resolve(
            ['REQUEST_URI' => '/some?foo=bar'],
            [],
            $this->application
        );

        $this->assertEquals(
        // when called, $controller will return its own name
            'Common\Web\Fixtures\Application::someController',
            $controller()
        );
    }

    /**
     * @test
     */
    public function it_defaults_to_index_if_request_uri_is_empty(): void
    {
        $controller = ControllerResolver::resolve(
            ['REQUEST_URI' => ''],
            [],
            $this->application
        );

        $this->assertEquals(
        // when called, $controller will return its own name
            'Common\Web\Fixtures\Application::indexController',
            $controller()
        );
    }
}
