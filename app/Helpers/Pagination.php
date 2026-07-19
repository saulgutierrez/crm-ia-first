<?php

declare(strict_types=1);

namespace App\Helpers;

class Pagination
{
    /**
     * Build pagination metadata.
     */
    public static function metadata(int $total, int $page, int $perPage): array
    {
        $totalPages = (int) ceil($total / max($perPage, 1));

        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
            'prev_page' => max($page - 1, 1),
            'next_page' => min($page + 1, $totalPages),
        ];
    }

    /**
     * Calculate offset for SQL LIMIT clause.
     */
    public static function offset(int $page, int $perPage): int
    {
        return ($page - 1) * $perPage;
    }

    /**
     * Get the current page from request, defaulting to 1.
     */
    public static function currentPage(): int
    {
        return max(1, (int) ($_GET['page'] ?? 1));
    }

    /**
     * Get the per-page value from request, defaulting to configured value.
     */
    public static function perPage(int $default = 15): int
    {
        $perPage = (int) ($_GET['per_page'] ?? $default);
        return min(max($perPage, 5), 100); // Clamp between 5 and 100
    }
}
