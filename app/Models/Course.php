<?php

namespace App\Models;

use PDO;

class Course
{
    private PDO $db;

    public int $id = -1;
    public string $courseName;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function fill(array $data): Course
    {
        $this->courseName = $data['courseName'];
        return $this;
    }

    private function fillFromDbRow(array $row): void
    {
        $this->id = (int)$row['id'];
        $this->courseName = $row['courseName'];
    }

    public function save(): bool
    {
        if ($this->id >= 0) {
            // Update
            $statement = $this->db->prepare(
                'UPDATE courses SET courseName = :name WHERE id = :id'
            );
            return $statement->execute([
                'id' => $this->id,
                'name' => $this->courseName
            ]);
        } else {
            // Insert
            $statement = $this->db->prepare(
                'INSERT INTO courses (courseName) VALUES (:name)'
            );
            $success = $statement->execute([
                'name' => $this->courseName
            ]);
            if ($success) {
                $this->id = $this->db->lastInsertId();
            }
            return $success;
        }
    }

    public function delete(): bool
    {
        // Kiểm tra xem có sinh viên nào thuộc khóa học này không
        $checkStudents = $this->db->prepare('SELECT COUNT(*) FROM users WHERE course_id = :course_id');
        $checkStudents->execute(['course_id' => $this->id]);
        $studentCount = $checkStudents->fetchColumn();

        if ($studentCount > 0) {
            // Có sinh viên liên kết, không xóa được
            return false;
        }

        // Nếu không có sinh viên nào liên kết, tiến hành xóa khóa học
        $statement = $this->db->prepare('DELETE FROM courses WHERE id = :id');
        return $statement->execute(['id' => $this->id]);
    }

    public function find(int $id): ?Course
    {
        $statement = $this->db->prepare('SELECT * FROM courses WHERE id = :id');
        $statement->execute(['id' => $id]);

        if ($row = $statement->fetch()) {
            $this->fillFromDbRow($row);
            return $this;
        }

        return null;
    }

    public function getAllCourses(): array
    {
        $statement = $this->db->query('SELECT * FROM courses ORDER BY courseName');
        $courses = [];

        while ($row = $statement->fetch()) {
            $course = new Course($this->db);
            $course->fillFromDbRow($row);
            $courses[] = $course;
        }

        return $courses;
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['courseName'])) {
            $errors['courseName'] = 'Tên khóa học không được để trống.';
        } else {
            // Kiểm tra tên đã tồn tại chưa
            $statement = $this->db->prepare(
                'SELECT COUNT(*) FROM courses WHERE courseName = :name'
            );
            $statement->execute(['name' => $data['courseName']]);

            if ($statement->fetchColumn() > 0) {
                $errors['courseName'] = 'Tên khóa học đã tồn tại.';
            }
        }

        return $errors;
    }

    public function validateEdit(array $data, string $currentName): array
    {
        $errors = [];

        if (empty($data['courseName'])) {
            $errors['courseName'] = 'Tên khóa học không được để trống.';
        } elseif ($data['courseName'] !== $currentName) {
            // Kiểm tra trùng lặp nếu tên thay đổi
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM courses WHERE courseName = :name');
            $stmt->execute(['name' => $data['courseName']]);
            if ($stmt->fetchColumn() > 0) {
                $errors['courseName'] = 'Tên khóa học đã tồn tại.';
            }
        }

        return $errors;
    }
}
