<?php

namespace App\Models;

use PDO;

class Subject
{
    private PDO $db;

    public int $id = -1;
    public string $subjectCode;
    public string $subjectName;
    public int $credits;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): Subject
    {
        $statement = $this->db->prepare("SELECT * FROM subjects WHERE $column = :value");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch();
        if ($row) {
            $this->fillFromDbRow($row);
        }
        return $this;
    }

    public function save(): bool
    {
        if ($this->id >= 0) {
            $statement = $this->db->prepare(
                'UPDATE subjects SET subjectCode = :code, subjectName = :name, credits = :credits WHERE id = :id'
            );
            return $statement->execute([
                'id' => $this->id,
                'code' => $this->subjectCode,
                'name' => $this->subjectName,
                'credits' => $this->credits,
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO subjects (subjectCode, subjectName, credits) VALUES (:code, :name, :credits)'
            );
            $result = $statement->execute([
                'code' => $this->subjectCode,
                'name' => $this->subjectName,
                'credits' => $this->credits,
            ]);

            if ($result) {
                $this->id = $this->db->lastInsertId();
            }

            return $result;
        }
    }

    public function fill(array $data): Subject
    {
        $this->subjectCode = $data['subjectCode'];
        $this->subjectName = $data['subjectName'];
        $this->credits = (int) $data['credits'];
        return $this;
    }

    private function fillFromDbRow(array $row)
    {
        $this->id = $row['id'];
        $this->subjectCode = $row['subjectCode'];
        $this->subjectName = $row['subjectName'];
        $this->credits = $row['credits'];
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (!$data['subjectCode']) {
            $errors['subjectCode'] = 'Mã học phần không được để trống.';
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM subjects WHERE subjectCode = :code');
            $stmt->execute(['code' => $data['subjectCode']]);
            if ($stmt->fetchColumn() > 0) {
                $errors['subjectCode'] = 'Mã học phần đã tồn tại.';
            }
        }

        if (!$data['subjectName']) {
            $errors['subjectName'] = 'Tên học phần không được để trống.';
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM subjects WHERE subjectName = :name');
            $stmt->execute(['name' => $data['subjectName']]);
            if ($stmt->fetchColumn() > 0) {
                $errors['subjectName'] = 'Tên học phần đã tồn tại.';
            }
        }

        if (empty($data['credits']) || !is_numeric($data['credits']) || (int)$data['credits'] <= 0) {
            $errors['credits'] = 'Số tín chỉ phải là số nguyên dương.';
        }

        return $errors;
    }

    public function validateEdit(array $data, string $currentCode, string $currentName): array
    {
        $errors = [];

        if (empty($data['subjectCode'])) {
            $errors['subjectCode'] = 'Mã học phần không được để trống.';
        } elseif ($data['subjectCode'] !== $currentCode) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM subjects WHERE subjectCode = :code");
            $stmt->execute(['code' => $data['subjectCode']]);
            if ($stmt->fetchColumn() > 0) {
                $errors['subjectCode'] = 'Mã học phần đã tồn tại.';
            }
        }

        if (empty($data['subjectName'])) {
            $errors['subjectName'] = 'Tên học phần không được để trống.';
        } elseif ($data['subjectName'] !== $currentName) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM subjects WHERE subjectName = :name");
            $stmt->execute(['name' => $data['subjectName']]);
            if ($stmt->fetchColumn() > 0) {
                $errors['subjectName'] = 'Tên học phần đã tồn tại.';
            }
        }

        if (empty($data['credits']) || !is_numeric($data['credits']) || (int)$data['credits'] <= 0) {
            $errors['credits'] = 'Số tín chỉ phải là số nguyên dương.';
        }

        return $errors;
    }

    public function getAllSubjects(): array
    {
        $statement = $this->db->prepare('SELECT * FROM subjects');
        $statement->execute();

        $subjects = [];
        while ($row = $statement->fetch()) {
            $subject = new Subject($this->db);
            $subject->fillFromDbRow($row);
            $subjects[] = $subject;
        }

        return $subjects;
    }

    public function find(int $id): ?Subject
    {
        $statement = $this->db->prepare('SELECT * FROM subjects WHERE id = :id');
        $statement->execute(['id' => $id]);

        if ($row = $statement->fetch()) {
            $this->fillFromDbRow($row);
            return $this;
        }

        return null;
    }

    public function delete(): bool
    {
        // Kiểm tra xem học phần có đang được sử dụng trong bảng teaching_assignments không
        $checkStmt = $this->db->prepare('SELECT COUNT(*) FROM teaching_assignments WHERE subject_id = :id');
        $checkStmt->execute(['id' => $this->id]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            // Nếu có phân công giảng dạy sử dụng học phần này => không thể xóa
            return false;
        }

        // Nếu không bị ràng buộc, thì thực hiện xóa
        $statement = $this->db->prepare('DELETE FROM subjects WHERE id = :id');
        return $statement->execute(['id' => $this->id]);
    }

}
