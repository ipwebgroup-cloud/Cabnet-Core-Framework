<?php
declare(strict_types=1);

final class FileLogger implements LoggerInterface
{
    public function __construct(
        private string $path,
        private string $level = 'error'
    ) {
    }

    public function debug(string $message, array $context = []): void
    {
        $this->write('debug', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    private function write(string $level, string $message, array $context = []): void
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $line = sprintf(
            "[%s] %s: %s %s%s",
            date('c'),
            strtoupper($level),
            $message,
            empty($context) ? '' : json_encode($context, JSON_UNESCAPED_UNICODE),
            PHP_EOL
        );

        file_put_contents($this->path, $line, FILE_APPEND);
    }
}
