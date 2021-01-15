<?php
declare(strict_types=1);

namespace Common\Web\Fixtures;

final class Application
{
    public function indexController(): string
    {
        return __METHOD__;
    }

    public function someController(): string
    {
        return __METHOD__;
    }

    /**
     * @return array<string,string>
     */
    public function withArgumentsController(string $id, string $orderId): array
    {
        return func_get_args();
    }
}
