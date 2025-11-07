<?php

namespace App\Models;

use PDO;

class Classroom
{
    private PDO $db;

    public int $id = -1;
    public string $classroomName;
    public int $capacity = 0;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function fill(array $data): Classroom
    {
        $this->classroomName = $data['classroomName'];
        $this->capacity = isset($data['capacity']) ? (int)$data['capacity'] : 0;
        return $this;
    }

    private function fillFromDbRow(array $row): void
    {
        $this->id = (int)$row['id'];
        $this->classroomName = $row['classroomName'];
        $this->capacity = (int)($row['capacity'] ?? 0);
    }

    public function save(): bool
    {
        if ($this->id >= 0) {
            // Update
            $statement = $this->db->prepare(
                'UPDATE classrooms SET classroomName = :name, capacity = :capacity WHERE id = :id'
            );
            return $statement->execute([
                'id' => $this->id,
                'name' => $this->classroomName,
                'capacity' => $this->capacity
            ]);
        } else {
            // Insert
            $statement = $this->db->prepare(
                'INSERT INTO classrooms (classroomName, capacity) VALUES (:name, :capacity)'
            );
            $success = $statement->execute([
                'name' => $this->classroomName,
                'capacity' => $this->capacity
            ]);
            if ($success) {
                $this->id = $this->db->lastInsertId();
            }
            return $success;
        }
    }

    public function delete(): bool
    {
        // Kiểm tra xem phòng học có đang được sử dụng trong teaching_assignments không
        $checkStmt = $this->db->prepare('SELECT COUNT(*) FROM teaching_assignments WHERE classroom_id = :id');
        $checkStmt->execute(['id' => $this->id]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            // Nếu có phân công giảng dạy sử dụng phòng học này thì không thể xóa
            return false;
        }

        // Nếu không bị ràng buộc, thì thực hiện xóa
        $statement = $this->db->prepare('DELETE FROM classrooms WHERE id = :id');
        return $statement->execute(['id' => $this->id]);
    }

    public function find(int $id): ?Classroom
    {
        $statement = $this->db->prepare('SELECT * FROM classrooms WHERE id = :id');
        $statement->execute(['id' => $id]);

        if ($row = $statement->fetch()) {
            $this->fillFromDbRow($row);
            return $this;
        }

        return null;
    }

    public function getAllClassrooms(): array
    {
        $statement = $this->db->query('SELECT * FROM classrooms ORDER BY classroomName');
        $classrooms = [];

        while ($row = $statement->fetch()) {
            $classroom = new Classroom($this->db);
            $classroom->fillFromDbRow($row);
            $classrooms[] = $classroom;
        }

        return $classrooms;
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['classroomName'])) {
            $errors['classroomName'] = 'Tên phòng học không được để trống.';
        } else {
            $statement = $this->db->prepare(
                'SELECT COUNT(*) FROM classrooms WHERE classroomName = :name'
            );
            $statement->execute(['name' => $data['classroomName']]);

            if ($statement->fetchColumn() > 0) {
                $errors['classroomName'] = 'Tên phòng học đã tồn tại.';
            }
        }

        if (!isset($data['capacity']) || !is_numeric($data['capacity']) || (int)$data['capacity'] <= 0) {
            $errors['capacity'] = 'Sức chứa phải là số nguyên dương.';
        }

        return $errors;
    }

    public function validateEdit(array $data, string $currentName): array
    {
        $errors = [];

        if (empty($data['classroomName'])) {
            $errors['classroomName'] = 'Tên phòng học không được để trống.';
        } elseif ($data['classroomName'] !== $currentName) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM classrooms WHERE classroomName = :name');
            $stmt->execute(['name' => $data['classroomName']]);
            if ($stmt->fetchColumn() > 0) {
                $errors['classroomName'] = 'Tên phòng học đã tồn tại.';
            }
        }

        if (!isset($data['capacity']) || !is_numeric($data['capacity']) || (int)$data['capacity'] <= 0) {
            $errors['capacity'] = 'Sức chứa phải là số nguyên dương.';
        }

        return $errors;
    }
}
