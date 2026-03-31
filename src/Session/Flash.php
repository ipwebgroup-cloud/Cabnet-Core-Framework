<?php
declare(strict_types=1);

namespace Cabnet\Session;

class Flash
{
    public function __construct(private Session $session)
    {
    }

    public function add(string $type, string $message): void
    {
        $messages = $this->session->get('_flash', []);
        $messages[$type][] = $message;
        $this->session->set('_flash', $messages);
    }

    public function all(): array
    {
        $messages = $this->session->get('_flash', []);
        $this->session->forget('_flash');
        return is_array($messages) ? $messages : [];
    }

    public function has(): bool
    {
        $messages = $this->session->get('_flash', []);
        return !empty($messages);
    }
}
