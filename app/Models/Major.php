<?php

namespace App\Models;

use PDO;

class Major
{
    private PDO $db;

    public int $id = -1;
    public string $majorCode;
    public string $majorName;
    public int $department_id;

    public string $department_name;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): Major
    {
        $statement = $this->db->prepare("SELECT * FROM majors WHERE $column = :value");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch();
        if ($row) {
            $this->fillFromDbRow($row);
        }
        return $this;
    }

    public function save(): bool
    {
        $result = false;

        if ($this->id >= 0) {
            $statement = $this->db->prepare(
                'UPDATE majors SET majorCode = :code, majorName = :name, department_id = :department_id WHERE id = :id'
            );
            $result = $statement->execute([
                'id' => $this->id,
                'code' => $this->majorCode,
                'name' => $this->majorName,
                'department_id' => $this->department_id,
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO majors (majorCode, majorName, department_id) VALUES (:code, :name, :department_id)'
            );
            $result = $statement->execute([
                'code' => $this->majorCode,
                'name' => $this->majorName,
                'department_id' => $this->department_id,
            ]);

            if ($result) {
                $this->id = $this->db->lastInsertId();
            }
        }

        return $result;
    }

    public function fill(array $data): Major
    {
        $this->majorCode = $data['majorCode'];
        $this->majorName = $data['majorName'];
        $this->department_id = $data['department_id'];
        return $this;
    }

    private function fillFromDbRow(array $row)
    {
        $this->id = $row['id'];
        $this->majorCode = $row['majorCode'];
        $this->majorName = $row['majorName'];
        $this->department_id = $row['department_id'];
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (!$data['majorCode']) {
            $errors['majorCode'] = 'Mã ngành không được để trống.';
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM majors WHERE majorCode = :majorCode");
            $stmt->execute(['majorCode' => $data['majorCode']]);
            if ($stmt->fetchColumn() > 0) {
                $errors['majorCode'] = 'Mã ngành đã tồn tại.';
            }
        }

        if (!$data['majorName']) {
            $errors['majorName'] = 'Tên ngành không được để trống.';
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM majors WHERE majorName = :majorName");
            $stmt->execute(['majorName' => $data['majorName']]);
            if ($stmt->fetchColumn() > 0) {
                $errors['majorName'] = 'Tên ngành đã tồn tại.';
            }
        }

        if (empty($data['department_id']) || !is_numeric($data['department_id'])) {
            $errors['department_id'] = 'Vui lòng chọn khoa.';
        }

        return $errors;
    }

    public function validateEdit(array $data, string $currentMajorName, int $currentMajorId): array
    {
        $errors = [];

        if (empty($data['majorCode'])) {
            $errors['majorCode'] = 'Mã ngành không được để trống.';
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM majors WHERE majorCode = :majorCode AND id != :id"
            );
            $stmt->execute([
                'majorCode' => $data['majorCode'],
                'id' => $currentMajorId
            ]);
            if ($stmt->fetchColumn() > 0) {
                $errors['majorCode'] = 'Mã ngành đã tồn tại.';
            }
        }

        if (empty($data['majorName'])) {
            $errors['majorName'] = 'Tên ngành không được để trống.';
        } else {
            if ($data['majorName'] !== $currentMajorName) {
                $stmt = $this->db->prepare(
                    "SELECT COUNT(*) FROM majors WHERE majorName = :majorName AND id != :id"
                );
                $stmt->execute([
                    'majorName' => $data['majorName'],
                    'id' => $currentMajorId
                ]);
                if ($stmt->fetchColumn() > 0) {
                    $errors['majorName'] = 'Tên ngành đã tồn tại.';
                }
            }
        }

        if (empty($data['department_id']) || !is_numeric($data['department_id'])) {
            $errors['department_id'] = 'Vui lòng chọn khoa.';
        }

        return $errors;
    }

    public function getAllMajors(): array
    {
        $statement = $this->db->prepare(
            'SELECT majors.*, 
                    departments.departmentName AS department_name
            FROM majors
            LEFT JOIN departments ON majors.department_id = departments.id'
        );
        $statement->execute();

        $majors = [];
        while ($row = $statement->fetch()) {
            $major = new Major($this->db);
            $major->fillFromDbRow($row);
            $major->department_name = $row['department_name'];
            $majors[] = $major;
        }

        return $majors;
    }

    public function find(int $id): ?Major
    {
        $statement = $this->db->prepare(
            'SELECT * FROM majors WHERE id = :id'
        );
        $statement->execute(['id' => $id]);

        if ($row = $statement->fetch()) {
            $this->fillFromDbRow($row);
            return $this;
        }

        return null;
    }

    public function delete(): bool
    {
        $checkUsers = $this->db->prepare(
            'SELECT COUNT(*) FROM users WHERE major_id = :id'
        );
        $checkUsers->execute(['id' => $this->id]);
        $userCount = $checkUsers->fetchColumn();

        if ($userCount > 0) {
            return false;
        }

        $statement = $this->db->prepare(
            'DELETE FROM majors WHERE id = :id'
        );
        return $statement->execute(['id' => $this->id]);
    }

}
