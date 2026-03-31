<?php
declare(strict_types=1);

final class Paginator
{
    public function __construct(
        private int $page,
        private int $perPage,
        private int $total
    ) {
    }

    public function page(): int
    {
        return $this->page;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function pages(): int
    {
        return max(1, (int)ceil($this->total / max(1, $this->perPage)));
    }

    public function offset(): int
    {
        return max(0, ($this->page - 1) * $this->perPage);
    }

    public function hasPages(): bool
    {
        return $this->pages() > 1;
    }

    public function previousPage(): int
    {
        return max(1, $this->page - 1);
    }

    public function nextPage(): int
    {
        return min($this->pages(), $this->page + 1);
    }

    public function pageRange(int $window = 2): array
    {
        $start = max(1, $this->page - $window);
        $end = min($this->pages(), $this->page + $window);
        return range($start, $end);
    }
}
