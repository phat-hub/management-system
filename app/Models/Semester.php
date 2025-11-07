<?php

namespace App\Models;

use PDO;

class Semester
{
    private PDO $db;

    public int $id = -1;
    public string $semester;      // học kỳ, ví dụ: "1", "2"
    public string $academicYear;  // năm học, ví dụ: "2023-2024"

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): Semester
    {
        $statement = $this->db->prepare("SELECT * FROM semesters WHERE $column = :value");
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
                'UPDATE semesters SET semester = :semester, academicYear = :academicYear WHERE id = :id'
            );
            $result = $statement->execute([
                'id' => $this->id,
                'semester' => $this->semester,
                'academicYear' => $this->academicYear,
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO semesters (semester, academicYear) VALUES (:semester, :academicYear)'
            );
            $result = $statement->execute([
                'semester' => $this->semester,
                'academicYear' => $this->academicYear,
            ]);

            if ($result) {
                $this->id = $this->db->lastInsertId();
            }
        }

        return $result;
    }

    public function fill(array $data): Semester
    {
        $this->semester = $data['semester'];
        $this->academicYear = $data['academicYear'];
        return $this;
    }

    private function fillFromDbRow(array $row)
    {
        $this->id = $row['id'];
        $this->semester = $row['semester'];
        $this->academicYear = $row['academicYear'];
    }

    public function validate(array $data): array
    {
        $errors = [];

        // Kiểm tra học kỳ không được bỏ trống và hợp lệ
        if (empty($data['semester']) || !in_array($data['semester'], ['1', '2', '3'])) {
            $errors['semester'] = 'Học kỳ không hợp lệ (chỉ chấp nhận 1, 2 hoặc 3).';
        }

        // Kiểm tra năm học không được bỏ trống
        if (empty($data['academicYear'])) {
            $errors['academicYear'] = 'Năm học không được để trống.';
        } else {
            // Kiểm tra định dạng năm học như "2023-2024"
            if (!preg_match('/^\d{4}-\d{4}$/', $data['academicYear'])) {
                $errors['academicYear'] = 'Năm học phải có định dạng "YYYY-YYYY", ví dụ: 2023-2024.';
            }
        }

        // Kiểm tra học kỳ và năm học không trùng lặp
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM semesters WHERE semester = :semester AND academicYear = :academicYear');
        $stmt->execute([
            'semester' => $data['semester'],
            'academicYear' => $data['academicYear'],
        ]);
        if ($stmt->fetchColumn() > 0) {
            $errors['semester'] = 'Học kỳ và năm học này đã tồn tại.';
        }

        return $errors;
    }

    public function validateEdit(array $data, $currentSemester, $currentAcademicYear): array
    {
        $errors = [];

        // Kiểm tra học kỳ không được bỏ trống và hợp lệ
        if (empty($data['semester']) || !in_array($data['semester'], ['1', '2', '3'])) {
            $errors['semester'] = 'Học kỳ không hợp lệ (chỉ chấp nhận 1, 2 hoặc 3).';
        }

        // Kiểm tra năm học không được bỏ trống
        if (empty($data['academicYear'])) {
            $errors['academicYear'] = 'Năm học không được để trống.';
        } else {
            if (!preg_match('/^\d{4}-\d{4}$/', $data['academicYear'])) {
                $errors['academicYear'] = 'Năm học phải có định dạng "YYYY-YYYY", ví dụ: 2023-2024.';
            }
        }

        // Kiểm tra trùng lặp khi sửa (nếu có thay đổi)
        if ($data['semester'] !== $currentSemester || $data['academicYear'] !== $currentAcademicYear) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM semesters WHERE semester = :semester AND academicYear = :academicYear');
            $stmt->execute([
                'semester' => $data['semester'],
                'academicYear' => $data['academicYear'],
            ]);
            if ($stmt->fetchColumn() > 0) {
                $errors['semester'] = 'Học kỳ và năm học này đã tồn tại.';
            }
        }

        return $errors;
    }

    public function getAllSemesters(): array
    {
        $statement = $this->db->prepare('SELECT * FROM semesters ORDER BY academicYear DESC, semester ASC');
        $statement->execute();

        $semesters = [];
        while ($row = $statement->fetch()) {
            $semester = new Semester($this->db);
            $semester->fillFromDbRow($row);
            $semesters[] = $semester;
        }

        return $semesters;
    }

    public function find(int $id): ?Semester
    {
        $statement = $this->db->prepare('SELECT * FROM semesters WHERE id = :id');
        $statement->execute(['id' => $id]);

        if ($row = $statement->fetch()) {
            $this->fillFromDbRow($row);
            return $this;
        }

        return null;
    }

    public function delete(): bool
    {
        // Kiểm tra xem học kỳ có đang được sử dụng trong bảng teaching_assignments không
        $checkStmt = $this->db->prepare('SELECT COUNT(*) FROM teaching_assignments WHERE semester_id = :id');
        $checkStmt->execute(['id' => $this->id]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            // Nếu có phân công giảng dạy sử dụng học kỳ này => không thể xóa
            return false;
        }

        // Nếu không bị ràng buộc, thì thực hiện xóa
        $statement = $this->db->prepare('DELETE FROM semesters WHERE id = :id');
        return $statement->execute(['id' => $this->id]);
    }

    public function getLatestSemester(): ?Semester
    {
        $stmt = $this->db->prepare("SELECT * FROM semesters ORDER BY id DESC LIMIT 1");
        $stmt->execute();

        $row = $stmt->fetch();
        if ($row) {
            $semester = new Semester($this->db);
            $semester->fillFromDbRow($row);
            return $semester;
        }

        return null;
    }

    public function hasAny(): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM semesters');
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

}
