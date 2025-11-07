<?php

namespace App\Models;

use PDO;

class TeachingAssignment
{
    private PDO $db;

    public int $id = -1;
    public int $subject_id;
    public int $semester_id;
    public int $user_id;
    public int $classroom_id;
    public string $day_of_week;
    public int $start_period = 0;
    public int $end_period = 0;
    public int $slots_remaining;

    // Thuộc tính từ bảng liên kết
    public string $subject_name;
    public int $credits;
    public string $classroom_name;
    public int $classroom_capacity;
    public string $semester_name;
    public string $academic_year;
    public string $lecturer_name;
    
    public bool $is_registered = false;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    private function fillFromDbRow(array $row): void
    {
        $this->id = $row['id'];
        $this->subject_id = $row['subject_id'];
        $this->semester_id = $row['semester_id'];
        $this->user_id = $row['user_id'];
        $this->classroom_id = $row['classroom_id'];
        $this->day_of_week = $row['day_of_week'];
        $this->start_period = $row['start_period'];
        $this->end_period = $row['end_period'];
        $this->slots_remaining = $row['slots_remaining'];

        // Liên kết
        $this->subject_name = $row['subject_name'] ?? '';
        $this->credits = $row['credits'] ?? 0;
        $this->classroom_name = $row['classroom_name'] ?? '';
        $this->classroom_capacity = $row['classroom_capacity'] ?? 0;
        $this->semester_name = $row['semester_name'] ?? '';
        $this->academic_year = $row['academic_year'] ?? '';
        $this->lecturer_name = $row['lecturer_name'] ?? '';
    }

    public function getAll(int $semester_id): array
    {
        $statement = $this->db->prepare(
            'SELECT ta.*, 
                    s.subjectName AS subject_name, 
                    s.credits AS credits,
                    c.classroomName AS classroom_name,
                    c.capacity AS classroom_capacity,
                    sem.semester AS semester_name,
                    sem.academicYear AS academic_year,
                    u.name AS lecturer_name
            FROM teaching_assignments ta
            JOIN subjects s ON ta.subject_id = s.id
            JOIN classrooms c ON ta.classroom_id = c.id
            JOIN semesters sem ON ta.semester_id = sem.id
            JOIN users u ON ta.user_id = u.id
            WHERE ta.semester_id = :semester_id
            ORDER BY FIELD(ta.day_of_week, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"),
                    ta.start_period,
                    ta.end_period'
        );

        $statement->execute(['semester_id' => $semester_id]);

        $assignments = [];
        while ($row = $statement->fetch()) {
            $assignment = new TeachingAssignment($this->db);
            $assignment->fillFromDbRow($row);
            $assignments[] = $assignment;
        }

        return $assignments;
    }

    public function save(): bool
    {
        if ($this->id === -1) {
            $statement = $this->db->prepare(
                'INSERT INTO teaching_assignments (
                    subject_id, semester_id, user_id, classroom_id,
                    day_of_week, start_period, end_period, slots_remaining
                ) VALUES (
                    :subject_id, :semester_id, :user_id, :classroom_id,
                    :day_of_week, :start_period, :end_period, :slots_remaining
                )'
            );

            return $statement->execute([
                'subject_id' => $this->subject_id,
                'semester_id' => $this->semester_id,
                'user_id' => $this->user_id,
                'classroom_id' => $this->classroom_id,
                'day_of_week' => $this->day_of_week,
                'start_period' => $this->start_period,
                'end_period' => $this->end_period,
                'slots_remaining' => $this->slots_remaining
            ]);
        } else {
            $statement = $this->db->prepare(
                'UPDATE teaching_assignments SET
                    subject_id = :subject_id,
                    semester_id = :semester_id,
                    user_id = :user_id,
                    classroom_id = :classroom_id,
                    day_of_week = :day_of_week,
                    start_period = :start_period,
                    end_period = :end_period,
                    slots_remaining = :slots_remaining
                 WHERE id = :id'
            );

            return $statement->execute([
                'subject_id' => $this->subject_id,
                'semester_id' => $this->semester_id,
                'user_id' => $this->user_id,
                'classroom_id' => $this->classroom_id,
                'day_of_week' => $this->day_of_week,
                'start_period' => $this->start_period,
                'end_period' => $this->end_period,
                'slots_remaining' => $this->slots_remaining,
                'id' => $this->id
            ]);
        }
    }

    public function find(int $id): ?TeachingAssignment
    {
        $statement = $this->db->prepare(
            'SELECT ta.*, 
                    s.subjectName AS subject_name, 
                    s.credits AS credits,
                    c.classroomName AS classroom_name,
                    c.capacity AS classroom_capacity,
                    sem.semester AS semester_name,
                    sem.academicYear AS academic_year,
                    u.name AS lecturer_name
             FROM teaching_assignments ta
             JOIN subjects s ON ta.subject_id = s.id
             JOIN classrooms c ON ta.classroom_id = c.id
             JOIN semesters sem ON ta.semester_id = sem.id
             JOIN users u ON ta.user_id = u.id
             WHERE ta.id = :id'
        );

        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        if ($row) {
            $this->fillFromDbRow($row);
            return $this;
        }

        return null;
    }

    public function validate(array $data, int $id = 0): array
    {
        $errors = [];
        $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        if (empty($data['subject_id']) || !is_numeric($data['subject_id'])) {
            $errors['subject_id'] = 'Mã học phần không hợp lệ.';
        }

        if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
            $errors['user_id'] = 'Mã giảng viên không hợp lệ.';
        }

        if (empty($data['classroom_id']) || !is_numeric($data['classroom_id'])) {
            $errors['classroom_id'] = 'Mã phòng học không hợp lệ.';
        }

        if (empty($data['day_of_week']) || !in_array($data['day_of_week'], $validDays, true)) {
            $errors['day_of_week'] = 'Ngày trong tuần không hợp lệ.';
        }

        if (!is_numeric($data['start_period']) || $data['start_period'] < 1 || $data['start_period'] > 9) {
            $errors['start_period'] = 'Tiết bắt đầu không hợp lệ.';
        }
        
        if (!is_numeric($data['end_period']) || $data['end_period'] < $data['start_period'] || $data['end_period'] > 9) {
            $errors['end_period'] = 'Tiết kết thúc không hợp lệ.';
        }
        
        // Kiểm tra tiết bắt đầu và tiết kết thúc không nằm ở 2 buổi khác nhau
        if (
            is_numeric($data['start_period']) &&
            is_numeric($data['end_period']) &&
            empty($errors['start_period']) &&
            empty($errors['end_period'])
        ) {
            // Nếu tiết bắt đầu từ 1-5 và kết thúc từ 6-9
            if ($data['start_period'] <= 5 && $data['end_period'] >= 6) {
                $errors['start_period'] = 'Tiết bắt đầu và tiết kết thúc phải cùng buổi.';
                $errors['end_period'] = 'Tiết bắt đầu và tiết kết thúc phải cùng buổi.';
            }
        
        }        

        if (!empty($errors)) {
            return $errors; // Không cần kiểm tra tiếp nếu dữ liệu cơ bản sai
        }

        $db = $this->db;

        // Kiểm tra trùng lịch phòng học
        $stmt = $db->prepare("
            SELECT * FROM teaching_assignments 
            WHERE classroom_id = :classroom_id 
                AND day_of_week = :day_of_week
                AND semester_id = :semester_id
                AND NOT (end_period < :start_period OR start_period > :end_period)
                AND id != :id
        ");
        $stmt->execute([
            'classroom_id' => $data['classroom_id'],
            'day_of_week' => $data['day_of_week'],
            'semester_id' => $data['semester_id'],
            'start_period' => $data['start_period'],
            'end_period' => $data['end_period'],
            'id' => $id
        ]);
        if ($stmt->fetch()) {
            $errors['classroom_id'] = 'Phòng học đã được sử dụng trong khoảng thời gian này.';
        }

        // Kiểm tra trùng lịch giảng viên
        $stmt = $db->prepare("
            SELECT * FROM teaching_assignments 
            WHERE user_id = :user_id 
                AND day_of_week = :day_of_week
                AND semester_id = :semester_id
                AND NOT (end_period < :start_period OR start_period > :end_period)
                AND id != :id
        ");
        $stmt->execute([
            'user_id' => $data['user_id'],
            'day_of_week' => $data['day_of_week'],
            'semester_id' => $data['semester_id'],
            'start_period' => $data['start_period'],
            'end_period' => $data['end_period'],
            'id' => $id
        ]);
        if ($stmt->fetch()) {
            $errors['user_id'] = 'Giảng viên đã có lịch dạy trong khoảng thời gian này.';
        }

        return $errors;
    }

    public function fill(array $data): TeachingAssignment
    {
        $this->subject_id = (int)$data['subject_id'];
        $this->semester_id = (int)$data['semester_id'];
        $this->user_id = (int)$data['user_id'];
        $this->classroom_id = (int)$data['classroom_id'];
        $this->day_of_week = $data['day_of_week'];
        $this->start_period = (int)$data['start_period'];
        $this->end_period = (int)$data['end_period'];
        $this->slots_remaining = (int)$data['slots_remaining'];
        return $this;
    }

    public function delete(): bool
    {
        // Kiểm tra xem có bản ghi đăng ký học phần liên quan không
        $checkStmt = $this->db->prepare("
            SELECT COUNT(*) FROM subject_registrations 
            WHERE teaching_assignment_id = :id
        ");
        $checkStmt->execute(['id' => $this->id]);
        $count = (int)$checkStmt->fetchColumn();

        // Nếu có sinh viên đã đăng ký thì không được phép xóa
        if ($count > 0) {
            return false;
        }

        // Nếu không có thì thực hiện xóa
        $stmt = $this->db->prepare("DELETE FROM teaching_assignments WHERE id = :id");
        return $stmt->execute(['id' => $this->id]);
    }

    public function findByUserId(int $user_id, int $semester_id): array
    {
        $statement = $this->db->prepare(
            'SELECT ta.*, 
                    s.subjectName AS subject_name, 
                    s.credits AS credits,
                    c.classroomName AS classroom_name,
                    c.capacity AS classroom_capacity,
                    sem.semester AS semester_name,
                    sem.academicYear AS academic_year,
                    u.name AS lecturer_name
            FROM teaching_assignments ta
            JOIN subjects s ON ta.subject_id = s.id
            JOIN classrooms c ON ta.classroom_id = c.id
            JOIN semesters sem ON ta.semester_id = sem.id
            JOIN users u ON ta.user_id = u.id
            WHERE ta.user_id = :user_id AND ta.semester_id = :semester_id
            ORDER BY FIELD(ta.day_of_week, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"),
                    ta.start_period,
                    ta.end_period'
        );

        $statement->execute([
            'user_id' => $user_id,
            'semester_id' => $semester_id
        ]);

        $assignments = [];
        while ($row = $statement->fetch()) {
            $assignment = new TeachingAssignment($this->db);
            $assignment->fillFromDbRow($row);
            $assignments[] = $assignment;
        }

        return $assignments;
    }

    

}
