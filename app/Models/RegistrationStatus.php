<?php

namespace App\Models;

use PDO;

class RegistrationStatus
{
    private PDO $db;

    public int $id = -1;
    public bool $is_open;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function save(): bool
    {
        $statement = $this->db->prepare(
            "INSERT INTO registration_status (is_open) VALUES (:is_open)"
        );

        return $statement->execute([
            'is_open' => false
        ]);
    }

    public function open(): bool
    {
        $statement = $this->db->prepare("UPDATE registration_status SET is_open = :is_open LIMIT 1");
        return $statement->execute(['is_open' => 1]);
    }

    public function close(): bool
    {
        $statement = $this->db->prepare("UPDATE registration_status SET is_open = :is_open LIMIT 1");
        return $statement->execute(['is_open' => 0]);
    }

    public function isOpen(): bool
    {
        $statement = $this->db->query("SELECT is_open FROM registration_status LIMIT 1");
        $row = $statement->fetch();

        if ($row) {
            return (bool)$row['is_open'];
        }

        return false; // Mặc định nếu không có bản ghi nào
    }

    public function count(): int
    {
        $statement = $this->db->query("SELECT COUNT(*) as total FROM registration_status");
        $row = $statement->fetch();

        return (int)$row['total'];
    }

}
