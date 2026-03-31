<?php
declare(strict_types=1);

final class ProductRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'products';
    }

    public function create(array $data): bool
    {
        $sql = 'INSERT INTO `products` (`title`, `slug`, `status`, `summary`, `created_at`, `updated_at`)
                VALUES (:title, :slug, :status, :summary, NOW(), NOW())';

        return $this->db->execute($sql, [
            'title' => $data['title'] ?? '',
            'slug' => $data['slug'] ?? '',
            'status' => $data['status'] ?? '',
            'summary' => $data['summary'] ?? '',
        ]);
    }

    public function updateById(int $id, array $data): bool
    {
        $sql = 'UPDATE `products`
                SET `title` = :title,
                    `slug` = :slug,
                    `status` = :status,
                    `summary` = :summary,
                    `updated_at` = NOW()
                WHERE `id` = :id';

        return $this->db->execute($sql, [
            'id' => $id,
            'title' => $data['title'] ?? '',
            'slug' => $data['slug'] ?? '',
            'status' => $data['status'] ?? '',
            'summary' => $data['summary'] ?? '',
        ]);
    }
}
