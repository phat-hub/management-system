<?php

namespace App\Models;

use PDO;

class SubjectRegistration
{
    private PDO $db;

    public int $id = -1;
    public int $teaching_assignment_id;
    public int $student_id;
    public ?float $score = null;
    public ?string $exam_datetime = null;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    private function fillFromDbRow(array $row): void
    {
        $this->id = $row['id'];
        $this->teaching_assignment_id = $row['teaching_assignment_id'];
        $this->student_id = $row['student_id'];
        $this->score = isset($row['score']) ? (float)$row['score'] : null;
        $this->exam_datetime = $row['exam_datetime'] ?? null;
    }

    public function getAll(): array
    {
        $statement = $this->db->prepare("SELECT * FROM subject_registrations");
        $statement->execute();

        $registrations = [];
        while ($row = $statement->fetch()) {
            $registration = new SubjectRegistration($this->db);
            $registration->fillFromDbRow($row);
            $registrations[] = $registration;
        }

        return $registrations;
    }

    public function find(int $id): ?SubjectRegistration
    {
        $statement = $this->db->prepare("SELECT * FROM subject_registrations WHERE id = :id");
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();
        if ($row) {
            $this->fillFromDbRow($row);
            return $this;
        }

        return null;
    }

    public function save(): bool
    {
        if ($this->id === -1) {
            $this->db->beginTransaction();

            try {
                // Giảm slots_remaining trong teaching_assignments
                $update = $this->db->prepare("
                    UPDATE teaching_assignments 
                    SET slots_remaining = slots_remaining - 1 
                    WHERE id = :ta_id AND slots_remaining > 0
                ");
                $update->execute(['ta_id' => $this->teaching_assignment_id]);

                if ($update->rowCount() === 0) {
                    $this->db->rollBack();
                    return false;
                }

                // Thêm bản ghi đăng ký
                $insert = $this->db->prepare("
                    INSERT INTO subject_registrations (teaching_assignment_id, student_id, score, exam_datetime)
                    VALUES (:ta_id, :student_id, :score, :exam_datetime)
                ");
                $insert->execute([
                    'ta_id' => $this->teaching_assignment_id,
                    'student_id' => $this->student_id,
                    'score' => $this->score,
                    'exam_datetime' => $this->exam_datetime
                ]);

                $this->db->commit();
                return true;
            } catch (\Exception $e) {
                $this->db->rollBack();
                return false;
            }
        }

        return false; // Không hỗ trợ update bản ghi đã có ID
    }

    public function deleteByAssignmentAndStudent(int $teaching_assignment_id, int $student_id): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("SELECT id FROM subject_registrations WHERE teaching_assignment_id = :ta_id AND student_id = :student_id");
            $stmt->execute([
                'ta_id' => $teaching_assignment_id,
                'student_id' => $student_id
            ]);
            $row = $stmt->fetch();

            if (!$row) {
                $this->db->rollBack();
                return false;
            }

            $delete = $this->db->prepare("DELETE FROM subject_registrations WHERE teaching_assignment_id = :ta_id AND student_id = :student_id");
            $delete->execute([
                'ta_id' => $teaching_assignment_id,
                'student_id' => $student_id
            ]);

            $update = $this->db->prepare("
                UPDATE teaching_assignments 
                SET slots_remaining = slots_remaining + 1 
                WHERE id = :ta_id
            ");
            $update->execute(['ta_id' => $teaching_assignment_id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function fill(array $data): SubjectRegistration
    {
        $this->teaching_assignment_id = (int)$data['teaching_assignment_id'];
        $this->student_id = (int)$data['student_id'];
        $this->score = isset($data['score']) ? (float)$data['score'] : null;
        $this->exam_datetime = $data['exam_datetime'] ?? null;
        return $this;
    }

    public function findByStudentId(int $student_id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM subject_registrations WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $student_id]);

        $registrations = [];
        while ($row = $stmt->fetch()) {
            $registration = new SubjectRegistration($this->db);
            $registration->fillFromDbRow($row);
            $registrations[] = $registration;
        }

        return $registrations;
    }

    public function isRegistered(int $student_id, int $teaching_assignment_id): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM subject_registrations 
            WHERE student_id = :student_id AND teaching_assignment_id = :ta_id
        ");
        $stmt->execute([
            'student_id' => $student_id,
            'ta_id' => $teaching_assignment_id
        ]);

        $row = $stmt->fetch();
        return $row && $row['count'] > 0;
    }

    public function isStudentRegisteredSubjectInSemester(int $student_id, int $subject_id, int $semester_id): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM subject_registrations sr
            JOIN teaching_assignments ta ON sr.teaching_assignment_id = ta.id
            WHERE sr.student_id = :student_id
            AND ta.subject_id = :subject_id
            AND ta.semester_id = :semester_id
        ");
        $stmt->execute([
            'student_id' => $student_id,
            'subject_id' => $subject_id,
            'semester_id' => $semester_id,
        ]);
        $row = $stmt->fetch();
        return $row && $row['count'] > 0;
    }

    public function hasScheduleConflict(int $student_id, int $teaching_assignment_id): bool
    {
        $stmt = $this->db->prepare("
            SELECT ta.*
            FROM subject_registrations sr
            JOIN teaching_assignments ta ON sr.teaching_assignment_id = ta.id
            WHERE sr.student_id = :student_id
        ");
        $stmt->execute(['student_id' => $student_id]);
        $current = $this->db->prepare("SELECT * FROM teaching_assignments WHERE id = :id");
        $current->execute(['id' => $teaching_assignment_id]);
        $newAssignment = $current->fetch();

        foreach ($stmt->fetchAll() as $existing) {
            if (
                $existing['day_of_week'] === $newAssignment['day_of_week'] &&
                $existing['semester_id'] === $newAssignment['semester_id'] &&
                !($existing['end_period'] < $newAssignment['start_period'] || $existing['start_period'] > $newAssignment['end_period'])
            ) {
                return true;
            }
        }

        return false;
    }

    public function getStudentsByAssignment(int $teaching_assignment_id): array
    {
        $stmt = $this->db->prepare("
            SELECT sr.*, u.name, u.peopleId
            FROM subject_registrations sr
            JOIN users u ON sr.student_id = u.id
            WHERE sr.teaching_assignment_id = :ta_id
        ");
        $stmt->execute(['ta_id' => $teaching_assignment_id]);

        $students = [];
        while ($row = $stmt->fetch()) {
            $students[] = $row; 
        }

        return $students;
    }

    public function getAssignmentsByStudentAndSemester(int $student_id, int $semester_id): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                sr.id AS registration_id,
                ta.id AS assignment_id,
                ta.start_period AS start_period,
                ta.end_period AS end_period,
                ta.day_of_week AS day_of_week,
                s.subjectName AS subject_name,
                s.subjectCode AS subject_code,
                s.credits AS subject_credits,
                i.name AS lecturer_name,
                c.classroomName AS classroom_name,
                sr.score,
                sr.exam_datetime
            FROM subject_registrations sr
            JOIN teaching_assignments ta ON sr.teaching_assignment_id = ta.id
            JOIN subjects s ON ta.subject_id = s.id
            JOIN users i ON ta.user_id = i.id
            JOIN classrooms c ON ta.classroom_id = c.id
            WHERE sr.student_id = :student_id
            AND ta.semester_id = :semester_id
            ORDER BY ta.day_of_week ASC, ta.start_period ASC
        ");

        $stmt->execute([
            'student_id' => $student_id,
            'semester_id' => $semester_id
        ]);

        return $stmt->fetchAll();
    }

    public function updateExamDatetime(int $teaching_assignment_id, string $exam_datetime): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE subject_registrations 
                SET exam_datetime = :exam_datetime 
                WHERE teaching_assignment_id = :id
            ");
            $stmt->execute([
                'exam_datetime' => $exam_datetime,
                'id' => $teaching_assignment_id
            ]);
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateScore(int $registration_id, float $score): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE subject_registrations 
                SET score = :score 
                WHERE id = :id
            ");
            $stmt->execute([
                'score' => $score,
                'id' => $registration_id
            ]);
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function existsByTeachingAssignment(int $teaching_assignment_id): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count 
            FROM subject_registrations 
            WHERE teaching_assignment_id = :ta_id
        ");
        $stmt->execute(['ta_id' => $teaching_assignment_id]);

        $row = $stmt->fetch();
        return $row && $row['count'] > 0;
    }

    public function getExamDatetimeByAssignmentId(int $teaching_assignment_id): ?string
    {
        $stmt = $this->db->prepare("
            SELECT exam_datetime 
            FROM subject_registrations 
            WHERE teaching_assignment_id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $teaching_assignment_id]);

        $row = $stmt->fetch();
        return $row ? $row['exam_datetime'] : null;
    }

    public function getScoreById(int $id): ?float
    {
        $stmt = $this->db->prepare("
            SELECT score 
            FROM subject_registrations 
            WHERE id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        return $row && $row['score'] !== null ? (float)$row['score'] : null;
    }

    public function getTotalCreditsByStudentLatestSemester(int $student_id): int
    {
        // 1. Lấy semester mới nhất
        $stmt = $this->db->query("SELECT id FROM semesters ORDER BY id DESC LIMIT 1");
        $latestSemester = $stmt->fetch();
        if (!$latestSemester) {
            return 0; // Không có học kỳ nào
        }
        $latestSemesterId = (int)$latestSemester['id'];

        // 2. Lấy tổng tín chỉ đăng ký của sinh viên ở học kỳ này
        $stmt = $this->db->prepare("
            SELECT SUM(s.credits) AS total_credits
            FROM subject_registrations sr
            JOIN teaching_assignments ta ON sr.teaching_assignment_id = ta.id
            JOIN subjects s ON ta.subject_id = s.id
            WHERE sr.student_id = :student_id
            AND ta.semester_id = :semester_id
        ");
        $stmt->execute([
            'student_id' => $student_id,
            'semester_id' => $latestSemesterId
        ]);

        $result = $stmt->fetch();
        return $result['total_credits'] ? (int)$result['total_credits'] : 0;
    }

    public function getTotalCreditsByStudent(int $student_id): int
    {
        $stmt = $this->db->prepare("
            SELECT SUM(s.credits) AS total_credits
            FROM subject_registrations sr
            JOIN teaching_assignments ta ON sr.teaching_assignment_id = ta.id
            JOIN subjects s ON ta.subject_id = s.id
            WHERE sr.student_id = :student_id
        ");
        $stmt->execute([
            'student_id' => $student_id
        ]);

        $result = $stmt->fetch();
        return $result['total_credits'] ? (int)$result['total_credits'] : 0;
    }

    public function getAccumulatedScoresBySemester(int $student_id, int $semester_id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                ta.semester_id,
                SUM(s.credits * sr.score) AS total_weighted_score,
                SUM(s.credits) AS total_credits,
                SUM(CASE WHEN sr.score IS NULL THEN 1 ELSE 0 END) AS unscored
            FROM subject_registrations sr
            JOIN teaching_assignments ta ON sr.teaching_assignment_id = ta.id
            JOIN subjects s ON ta.subject_id = s.id
            WHERE sr.student_id = :student_id AND ta.semester_id = :semester_id
            GROUP BY ta.semester_id
        ");
        
        $stmt->execute([
            'student_id' => $student_id,
            'semester_id' => $semester_id
        ]);

        $row = $stmt->fetch();

        // Nếu không có dữ liệu cho học kỳ này
        if (!$row) {
            return null;
        }

        $totalCredits = (int)$row['total_credits'];
        $unscored = (int)$row['unscored'];

        // Chỉ tính điểm tích lũy nếu tất cả các môn đã có điểm
        if ($totalCredits > 0 && $unscored === 0) {
            $avg = round($row['total_weighted_score'] / $totalCredits, 2);
        } else {
            $avg = null;
        }

        return [
            'semester_id' => (int)$row['semester_id'],
            'average_score' => $avg,
            'total_credits' => $totalCredits,
            'all_scored' => $unscored === 0
        ];
    }

}
