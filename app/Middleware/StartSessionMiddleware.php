<?php
declare(strict_types=1);

final class StartSessionMiddleware implements MiddlewareInterface
{
    public function handle(App $app): ?Response
    {
        $app->session()->start();
        return null;
    }
}
