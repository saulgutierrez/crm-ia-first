<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HttpException;

abstract class BaseService
{
    /**
     * Validate that a required field is present.
     */
    protected function requireField(array $data, string $field, string $label = ''): void
    {
        if (empty($data[$field]) && $data[$field] !== '0') {
            $label = $label ?: $field;
            throw HttpException::validationError("El campo {$label} es obligatorio.");
        }
    }

    /**
     * Soft-delete pattern: set deleted_at instead of hard delete.
     */
    protected function applySoftDelete(string $table, string $primaryKey, int $id, \PDO $db): bool
    {
        $stmt = $db->prepare(
            "UPDATE {$table} SET deleted_at = NOW() WHERE {$primaryKey} = :id"
        );
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Return only non-deleted records condition.
     */
    protected function notDeleted(): string
    {
        return 'deleted_at IS NULL';
    }
}
