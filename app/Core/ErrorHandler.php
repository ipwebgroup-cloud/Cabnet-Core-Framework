<?php
declare(strict_types=1);

final class ErrorHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private bool $debug = false
    ) {
    }

    public function register(): void
    {
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
    }

    public function handleException(Throwable $e): void
    {
        $this->logger->error($e->getMessage(), [
            'type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');

        if ($this->debug) {
            echo '<h1>Application Error</h1>';
            echo '<pre>' . htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8') . '</pre>';
            return;
        }

        echo '<h1>500 Internal Server Error</h1><p>Something went wrong.</p>';
    }

    public function handleError(int $severity, string $message, string $file, int $line): bool
    {
        $this->logger->error($message, [
            'severity' => $severity,
            'file' => $file,
            'line' => $line,
        ]);

        if ($this->debug) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }

        return true;
    }
}
