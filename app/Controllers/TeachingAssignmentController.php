<?php

namespace App\Controllers;

use App\Models\TeachingAssignment;
use App\Models\Subject;
use App\Models\Semester;
use App\Models\User;
use App\Models\Classroom;
use App\Models\SubjectRegistration;
use App\Models\RegistrationStatus;
use App\Models\Major;
use App\Models\Department;
use App\Models\Course;

class TeachingAssignmentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // Hiển thị danh sách phân công giảng dạy
    public function teaching_assignment()
    {
        $authUser = AUTHGUARD()->user();
        $model = new TeachingAssignment(PDO());
        $subjectRegModel = new SubjectRegistration(PDO());
        $semesterModel = new Semester(PDO());

        // Xác định học kỳ được chọn: nếu có trong POST thì lấy, ngược lại lấy học kỳ mới nhất
        $selectedSemesterId = isset($_POST['semester_id']) && is_numeric($_POST['semester_id'])
            ? (int)$_POST['semester_id']
            : ($semesterModel->getLatestSemester()?->id ?? 0);
        // Lấy danh sách phân công giảng dạy tùy theo vai trò
        if ($authUser->role === 'admin' || $authUser->role === 'student') {
            $teachingAssignments = $model->getAll($selectedSemesterId);
        } else {
            $teachingAssignments = $model->findByUserId($authUser->id, $selectedSemesterId);
        }

        // Với sinh viên: kiểm tra đã đăng ký học phần nào chưa
        if ($authUser->role === 'student') {
            if(!(new RegistrationStatus(PDO()))->isOpen()){
                $messages = ['error' => 'Chưa tới thời gian đăng ký học phần.'];
                $major = (new Major(PDO()))->find(AUTHGUARD()->user()->major_id);
                $department = (new Department(PDO()))->find(AUTHGUARD()->user()->department_id);
                $course = (new Course(PDO()))->find(AUTHGUARD()->user()->course_id);
                $this->sendPage('/home/home', [
                    'majorName' => $major?->majorName,
                    'departmentName' => $department?->departmentName,
                    'courseName' => $course?->courseName,
                    'messages' => $messages
                ]);
            }
            foreach ($teachingAssignments as $ta) {
                $ta->is_registered = $subjectRegModel->isRegistered($authUser->id, $ta->id);
            }
        }

        // Gửi dữ liệu tới view
        $this->sendPage('/teaching_assignment/teaching_assignment', [
            'teachingAssignments' => $teachingAssignments,
            'semesters' => $semesterModel->getAllSemesters(),
            'selectedSemesterId' => $selectedSemesterId,
            'isOpen' => (new RegistrationStatus(PDO()))->isOpen()
        ]);
    }

    // Trang tạo mới phân công giảng dạy
    public function create()
    {
        $this->generateCsrfToken();

        $subjects = (new Subject(PDO()))->getAllSubjects();
        $semesters = (new Semester(PDO()))->getLatestSemester();
        $users = (new User(PDO()))->getAllLecturers();  
        $classrooms = (new Classroom(PDO()))->getAllClassrooms();

        $this->sendPage('teaching_assignment/create', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues(),
            'subjects' => $subjects,
            'latestSemester' => $semesters,
            'users' => $users,
            'classrooms' => $classrooms
        ]);
    }

    // Lưu phân công giảng dạy mới
    public function store()
    {
        $this->validateCsrfToken($_POST['csrf_token']);

        $data = [
            'subject_id' => isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : null,
            'semester_id' => isset($_POST['semester_id']) ? (int)$_POST['semester_id'] : null,
            'user_id' => isset($_POST['user_id']) ? (int)$_POST['user_id'] : null,
            'classroom_id' => isset($_POST['classroom_id']) ? (int)$_POST['classroom_id'] : null,
            'day_of_week' => trim($_POST['day_of_week'] ?? ''),
            'start_period' => isset($_POST['start_period']) ? (int)$_POST['start_period'] : null,
            'end_period' => isset($_POST['end_period']) ? (int)$_POST['end_period'] : null,
            'slots_remaining' => isset($_POST['slots_remaining']) ? (int)$_POST['slots_remaining'] : null,
        ];

        $teachingAssignment = new TeachingAssignment(PDO());

        $errors = $teachingAssignment->validate($data);

        if (empty($errors)) {
            $classroom = (new Classroom(PDO()))->find($data['classroom_id']);
            $data['slots_remaining'] = $classroom->capacity;
            $teachingAssignment->fill($data)->save();

            $messages = ['success' => 'Phân công giảng dạy đã được thêm thành công.'];
            redirect('/teaching_assignment', ['messages' => $messages]);
        }

        $this->saveFormValues($_POST);
        redirect('/teaching_assignment/create', ['errors' => $errors]);
    }

    // Trang chỉnh sửa phân công giảng dạy
    public function edit($id)
    {
        $this->generateCsrfToken();

        $teachingAssignment = (new TeachingAssignment(PDO()))->find($id);

        if (!$teachingAssignment) {
            $this->sendNotFound();
        }

        $subjects = (new Subject(PDO()))->getAllSubjects();
        $semesters = (new Semester(PDO()))->getAllSemesters();
        $users = (new User(PDO()))->getAllLecturers();
        $classrooms = (new Classroom(PDO()))->getAllClassrooms();

        $data = [
            'errors' => session_get_once('errors'),
            'assignment' => $teachingAssignment,
            'subjects' => $subjects,
            'semesters' => $semesters,
            'users' => $users,
            'classrooms' => $classrooms
        ];

        $this->sendPage('teaching_assignment/edit', $data);
    }

    // Cập nhật phân công giảng dạy
    public function update($id)
    {
        $this->validateCsrfToken($_POST['csrf_token']);

        $teachingAssignmentModel = new TeachingAssignment(PDO());
        $teachingAssignment = $teachingAssignmentModel->find($id);

        if (!$teachingAssignment) {
            $this->sendNotFound();
        }

        $data = [
            'subject_id' => isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : null,
            'semester_id' => isset($_POST['semester_id']) ? (int)$_POST['semester_id'] : null,
            'user_id' => isset($_POST['user_id']) ? (int)$_POST['user_id'] : null,
            'classroom_id' => isset($_POST['classroom_id']) ? (int)$_POST['classroom_id'] : null,
            'day_of_week' => trim($_POST['day_of_week'] ?? ''),
            'start_period' => isset($_POST['start_period']) ? (int)$_POST['start_period'] : null,
            'end_period' => isset($_POST['end_period']) ? (int)$_POST['end_period'] : null,
            'slots_remaining' => isset($_POST['slots_remaining']) ? (int)$_POST['slots_remaining'] : null,
        ];

        $errors = $teachingAssignment->validate($data, $id);

        if (empty($errors)) {
            $classroom = (new Classroom(PDO()))->find($data['classroom_id']);
            if($classroom->capacity !== $teachingAssignment->slots_remaining){
                $messages = ['error' => 'Phân công giảng dạy đã có sinh viên đăng ký.'];
                redirect('/teaching_assignment', ['messages' => $messages]);
            }
            $data['slots_remaining'] = $classroom->capacity;
            $teachingAssignment->fill($data)->save();

            $messages = ['success' => 'Phân công giảng dạy đã được cập nhật thành công.'];
            redirect('/teaching_assignment', ['messages' => $messages]);
        }

        redirect('/teaching_assignment/edit/' . $id, ['errors' => $errors]);
    }

    // Xóa phân công giảng dạy
    public function destroy($id)
    {
        $this->generateCsrfToken();

        $teachingAssignment = (new TeachingAssignment(PDO()))->find($id);

        if (!$teachingAssignment) {
            $this->sendNotFound();
        }

        $deleteSuccess = $teachingAssignment->delete();

        if (!$deleteSuccess) {
            $messages = ['error' => 'Không thể xóa phân công giảng dạy vì có dữ liệu phụ thuộc.'];
            redirect('/teaching_assignment', ['messages' => $messages]);
        }

        $messages = ['success' => 'Phân công giảng dạy đã được xóa thành công.'];
        redirect('/teaching_assignment', ['messages' => $messages]);
    }

    public function open()
    {
        $this->generateCsrfToken();
        if((new Semester(PDO()))->hasAny()){
            (new RegistrationStatus(PDO()))->open();
            $messages = ['success' => 'Mở đăng ký học phần thành công.'];
            redirect('/teaching_assignment', ['messages' => $messages]);
        }
        $messages = ['error' => 'Mở đăng ký học phần thất bại.'];
        redirect('/teaching_assignment', ['messages' => $messages]);
    }

    public function close()
    {
        $this->generateCsrfToken();
        if((new Semester(PDO()))->hasAny()){
            (new RegistrationStatus(PDO()))->close();
            $messages = ['success' => 'Đóng đăng ký học phần thành công.'];
            redirect('/teaching_assignment', ['messages' => $messages]);
        }
        $messages = ['error' => 'Đóng đăng ký học phần thất bại.'];
        redirect('/teaching_assignment', ['messages' => $messages]);
    }

    public function students(int $id)
    {
        $subjectRegModel = new SubjectRegistration(PDO());
        $students = $subjectRegModel->getStudentsByAssignment($id);

        // Gửi dữ liệu tới view
        $this->sendPage('/teaching_assignment/students', [
            'students' => $students
        ]);
    }
}
