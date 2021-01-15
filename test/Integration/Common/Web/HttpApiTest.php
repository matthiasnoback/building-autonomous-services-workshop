<?php
declare(strict_types=1);

namespace Test\Integration\Common\Web;

use Common\Web\HttpApi;
use PHPUnit\Framework\TestCase;

final class HttpApiTest extends TestCase
{
    /**
     * @test
     */
    public function it_fetches_a_response(): void
    {
        $response = HttpApi::fetchJsonResponse('https://www.google.com');

        self::assertNotEmpty($response);
    }
}
