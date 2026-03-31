<?php

declare(strict_types=1);

namespace Tests\Support;

use ReflectionClass;

final class ResponseInspector
{
    public static function snapshot(object $response): array
    {
        return [
            'statusCode' => self::read($response, 'statusCode'),
            'headers' => self::read($response, 'headers'),
            'body' => self::read($response, 'body'),
        ];
    }

    private static function read(object $response, string $property): mixed
    {
        $reflection = new ReflectionClass($response);
        $instance = $reflection;

        while (!$instance->hasProperty($property) && ($parent = $instance->getParentClass()) !== false) {
            $instance = $parent;
        }

        $prop = $instance->getProperty($property);
        $prop->setAccessible(true);

        return $prop->getValue($response);
    }
}
