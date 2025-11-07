<?php

namespace App\Controllers;

use App\Models\SubjectRegistration;
use App\Models\TeachingAssignment;
use App\Models\Semester;

class SubjectRegistrationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function subject_registration()
    {
        $semesterModel = new Semester(PDO());
        $selectedSemesterId = isset($_POST['semester_id']) && is_numeric($_POST['semester_id'])
            ? (int)$_POST['semester_id']
            : ($semesterModel->getLatestSemester()?->id ?? 0);
        $subjectRegModel = new SubjectRegistration(PDO());
        $studentSchedule = $subjectRegModel->getAssignmentsByStudentAndSemester(AUTHGUARD()->user()->id, $selectedSemesterId);

        // Gửi dữ liệu tới view
        $this->sendPage('subject_registration/subject_registration', [
            'studentSchedule' => $studentSchedule,
            'semesters' => $semesterModel->getAllSemesters(),
            'selectedSemesterId' => $selectedSemesterId,
        ]);
    }

    public function score_result()
    {
        $semesterModel = new Semester(PDO());
        $selectedSemesterId = isset($_POST['semester_id']) && is_numeric($_POST['semester_id'])
            ? (int)$_POST['semester_id']
            : ($semesterModel->getLatestSemester()?->id ?? 0);
        $subjectRegModel = new SubjectRegistration(PDO());
        $studentSchedule = $subjectRegModel->getAssignmentsByStudentAndSemester(AUTHGUARD()->user()->id, $selectedSemesterId);
        $accumulatedScore = $subjectRegModel->getAccumulatedScoresBySemester(AUTHGUARD()->user()->id, $selectedSemesterId);

        // Gửi dữ liệu tới view
        $this->sendPage('subject_registration/score_result', [
            'scoreList' => $studentSchedule,
            'semesters' => $semesterModel->getAllSemesters(),
            'selectedSemesterId' => $selectedSemesterId,
            'accumulatedScore' => $accumulatedScore
        ]);
    }

    public function tuition()
    {
        $subjectRegModel = new SubjectRegistration(PDO());
        $totalCredits = $subjectRegModel->getTotalCreditsByStudentLatestSemester(AUTHGUARD()->user()->id);
        $feePerCredit = 1328000;
        // Gửi dữ liệu tới view
        $this->sendPage('subject_registration/tuition', [
            'totalCredits' => $totalCredits,
            'feePerCredit' => $feePerCredit,
            'totalFee' => $totalCredits * $feePerCredit,
        ]);
    }

    public function store(int $id)
    {
        $this->validateCsrfToken($_POST['csrf_token']);

        $data = [
            'teaching_assignment_id' => $id,
            'student_id' => AUTHGUARD()->user()->id
        ];
        $teachingAssignmentModel = new TeachingAssignment(PDO());
        $ta = $teachingAssignmentModel->find($id);

        $registrationModel = new SubjectRegistration(PDO());
        if ($registrationModel->isStudentRegisteredSubjectInSemester(AUTHGUARD()->user()->id, $ta->subject_id, $ta->semester_id)) {
            // Đã đăng ký môn tương tự trong học kỳ này
            $messages = ['error' => 'Bạn đã đăng ký học phần này trong học kỳ này.'];
            redirect('/teaching_assignment', ['messages' => $messages]);
        }
        if($registrationModel->hasScheduleConflict(AUTHGUARD()->user()->id, $id)){
            $messages = ['error' => 'Trùng lịch không thể đăng ký.'];
            redirect('/teaching_assignment', ['messages' => $messages]);
        }

        $registrationModel->fill($data)->save();
        $messages = ['success' => 'Đăng ký học phần thành công.'];
        redirect('/teaching_assignment', ['messages' => $messages]);

    }

    public function destroy($id)
    {
        $this->generateCsrfToken();

        $registration = new SubjectRegistration(PDO());

        $deleted = $registration->deleteByAssignmentAndStudent($id, AUTHGUARD()->user()->id);

        if (!$deleted) {
            $messages = ['error' => 'Không thể hủy đăng ký học phần.'];
            redirect('/subject_registration', ['messages' => $messages]);
        }

        $messages = ['success' => 'Hủy đăng ký học phần thành công.'];
        redirect('/teaching_assignment', ['messages' => $messages]);
    }

    public function exam_datetime(int $id){
        $this->generateCsrfToken();

        $this->sendPage('teaching_assignment/exam_datetime', [
        'errors' => session_get_once('errors'),
        'id' => $id,
        'exam_datetime' => (new SubjectRegistration(PDO()))->getExamDatetimeByAssignmentId($id)
        ]);
    }

    public function update_exam_datetime(int $id)
    {
        // Xác thực CSRF token
        $this->validateCsrfToken($_POST['csrf_token']);

        // Khởi tạo model và tìm department theo ID
        $subjectRegModel = new SubjectRegistration(PDO());

        if(empty($_POST['exam_datetime'])){
            $errors['exam_datetime'] = 'Vui lòng chọn thời gian thi.';
            redirect('/teaching_assignment/exam_datetime/' . $id, ['errors' => $errors]);
        }
        if(!$subjectRegModel->existsByTeachingAssignment($id)){
            $messages = ['error' => 'Chưa có sinh viên đăng ký không thể cập nhật lịch thi.'];
            redirect('/teaching_assignment', ['messages' => $messages]);
        }

        $subjectRegModel->updateExamDatetime($id, $_POST['exam_datetime']);

        $messages = ['success' => 'Cập nhật thời gian thi thành công.'];
        redirect('/teaching_assignment', ['messages' => $messages]);
    }

    public function score(int $id){
        $this->generateCsrfToken();

        $this->sendPage('teaching_assignment/score', [
        'errors' => session_get_once('errors'),
        'id' => $id,
        'score' => (new SubjectRegistration(PDO()))->getScoreById($id)
        ]);
    }

    public function update_score(int $id)
    {
        // Xác thực CSRF token
        $this->validateCsrfToken($_POST['csrf_token']);

        // Khởi tạo model và tìm department theo ID
        $subjectRegModel = new SubjectRegistration(PDO());

        if(empty($_POST['score'])){
            $errors['score'] = 'Vui lòng nhập điểm.';
            redirect('/teaching_assignment/score/' . $id, ['errors' => $errors]);
        }

        $subjectRegModel->updateScore($id, $_POST['score']);

        $messages = ['success' => 'Cập nhật điểm thành công.'];
        redirect('/teaching_assignment/' . $subjectRegModel->find($id)->teaching_assignment_id . '/students' , ['messages' => $messages]);
    }

    public function getAccumulatedScoresBySemester(int $student_id, int $semester_id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                ta.semester_id,
                SUM(s.credits * sr.score) AS total_weighted_score,
                SUM(s.credits) AS total_credits,
                COUNT(*) FILTER (WHERE sr.score IS NULL) AS unscored
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
