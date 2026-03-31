<?php
declare(strict_types=1);

namespace Cabnet\Http;

use RuntimeException;

class ResponseResolver
{
    public function resolve(mixed $result, ?Response $fallback = null): Response
    {
        $response = $fallback ?? new Response();

        if ($result instanceof Response) {
            return $result;
        }

        if ($result instanceof \Response) {
            return $result;
        }

        if (is_string($result)) {
            return $response->html($result);
        }

        if ($result === null) {
            return $response->json(['status' => 'ok']);
        }

        throw new RuntimeException('Invalid route result from runtime pipeline.');
    }
}
