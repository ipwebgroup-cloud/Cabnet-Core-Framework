<?php
declare(strict_types=1);

interface MiddlewareInterface
{
    public function handle(App $app): ?Response;
}
