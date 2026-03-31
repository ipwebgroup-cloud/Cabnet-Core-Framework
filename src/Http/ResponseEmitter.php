<?php
declare(strict_types=1);

namespace Cabnet\Http;

class ResponseEmitter
{
    public function emit(Response $response): void
    {
        $response->send();
    }
}
