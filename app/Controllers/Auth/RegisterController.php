<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Models\Major;
use App\Models\Course;
use App\Models\Department;
use App\Controllers\Controller;

class RegisterController extends Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function createStudent()
  {
    $this->generateCsrfToken();
    $majors = (new Major(PDO()))->getAllMajors();
    $courses = (new Course(PDO()))->getAllCourses();
    
    $data = [
      'old' => $this->getSavedFormValues(),
      'errors' => session_get_once('errors'),
      'majors' => $majors,
      'courses' => $courses
    ];

    $this->sendPage('auth/student/create', $data);
  }

  public function createLecturer()
  {
    $this->generateCsrfToken();
    $departments = (new Department(PDO()))->getAllDepartments();
    
    $data = [
      'old' => $this->getSavedFormValues(),
      'errors' => session_get_once('errors'),
      'departments' => $departments
    ];

    $this->sendPage('auth/lecturer/create', $data);
  }

  public function storeStudent()
  {
    // Xác thực CSRF token
    $this->validateCsrfToken($_POST['csrf_token']);

    $data = $this->filterStudentData($_POST);
    $newUser = new User(PDO());

    $model_errors = $newUser->validate($data);

    if (empty($model_errors)) {
        $newUser->fill($data)->save();

        $messages = ['success' => 'Tài khoản đã được tạo thành công.'];
        redirect('/student', ['messages' => $messages]);
    }
    $this->saveFormValues($_POST, ['password', 'password_confirmation']);
    // Dữ liệu không hợp lệ...
    redirect('/student/register', ['errors' => $model_errors]);
  }

  public function storeLecturer()
  {
    // Xác thực CSRF token
    $this->validateCsrfToken($_POST['csrf_token']);

    $data = $this->filterLecturerData($_POST);
    $newUser = new User(PDO());
    $newUser->major_id = null;
    $newUser->course_id = null;
    $newUser->class = null;

    $model_errors = $newUser->validateLecturer($data);

    if (empty($model_errors)) {
        $newUser->fill($data)->save();

        $messages = ['success' => 'Tài khoản đã được tạo thành công.'];
        redirect('/lecturer', ['messages' => $messages]);
    }
    $this->saveFormValues($_POST, ['password', 'password_confirmation']);
    // Dữ liệu không hợp lệ...
    redirect('/lecturer/register', ['errors' => $model_errors]);
  }

  protected function filterStudentData(array $data)
  {

    if (empty($data['major_id']) || !is_numeric($data['major_id']) || empty($data['course_id']) || !is_numeric($data['course_id'])) {
        return [
            'name' => $data['name'] ?? null,
            'peopleId' => $data['peopleId'] ?? null,
            'gender' => $data['gender'] ?? null,
            'password' => $data['password'] ?? null,
            'password_confirmation' => $data['password_confirmation'] ?? null,
            'major_id' => $data['major_id'] ?? null,
            'department_id' => null,
            'class' => null,
            'course_id' => $data['course_id'] ?? null,
            'role' => 'student', // thêm role mặc định
        ];
    }

    $majorModel = new Major(PDO());
    $major = $majorModel->find((int)$data['major_id']);

    // Lấy mã khoa từ bảng Department
    $deptModel = new Department(PDO());
    $department = $deptModel->find($major->department_id);
    $departmentCode = $department->departmentCode;

    $courseModel = new Course(PDO());
    $course = $courseModel->find($data['course_id']);
    $courseName = $course->courseName;

    $userModel = new User(PDO());
    $studentCount = $userModel->countStudentsByMajorDeptCourse(
        (int)$data['major_id'],
        $major->department_id,
        (int)$data['course_id']
    );

    $label = 'A' . ceil(($studentCount + 1) / 60);
    $classCode = $departmentCode . $courseName . $major->majorCode . $label;

    return [
        'name' => $data['name'] ?? null,
        'peopleId' => $data['peopleId'] ?? null,
        'gender' => $data['gender'] ?? null,
        'password' => $data['password'] ?? null,
        'password_confirmation' => $data['password_confirmation'] ?? null,
        'major_id' => $data['major_id'] ?? null,
        'department_id' => $major->department_id,
        'class' => $classCode,
        'course_id' => $data['course_id'] ?? null,
        'role' => 'student',
    ];
  }

  protected function filterLecturerData(array $data)
  {
    return [
        'name' => $data['name'] ?? null,
        'peopleId' => $data['peopleId'] ?? null,
        'gender' => $data['gender'] ?? null,
        'password' => $data['password'] ?? null,
        'password_confirmation' => $data['password_confirmation'] ?? null,
        'major_id' => $data['major_id'] ?? null,
        'department_id' => $data['department_id'] ?? null,
        'class' => $data['class'] ?? null,
        'course_id' => $data['course_id'] ?? null,
        'role' => 'lecturer',
    ];
  }

}
