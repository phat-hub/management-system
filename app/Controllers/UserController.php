<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Major;
use App\Models\Course;
use App\Models\Department;

class UserController extends Controller
{
  public function __construct()
  {
    if (!AUTHGUARD()->isUserLoggedIn()) {
      redirect('/login');
    }

    parent::__construct();
  }

  public function student()
  {
    $students = AUTHGUARD()->user()?->getAllStudents() ?? [];
    $this->sendPage('/auth/student/student', [
      'students' => $students
    ]);
  }

  public function lecturer()
  {
    $lecturers = AUTHGUARD()->user()?->getAllLecturers() ?? [];
    $this->sendPage('/auth/lecturer/lecturer', [
      'lecturers' => $lecturers
    ]);
  }

  public function editStudent($id)
  {
      $this->generateCsrfToken();
      $majors = (new Major(PDO()))->getAllMajors();
      $courses = (new Course(PDO()))->getAllCourses();
      $user = (new User(PDO()))->where('id', $id);
      if (!$user || $user->id === -1) {
          $this->sendNotFound();
      }

      // Kiểm tra quyền truy cập
      $currentUser = AUTHGUARD()->user();
      $isAdmin = $currentUser->role === 'admin';

      $this->sendPage('auth/student/edit', [
          'student' => $user,
          'is_admin' => $isAdmin,
          'majors' => $majors,
          'courses' => $courses,
          'errors' => session_get_once('errors')
      ]);
  }

  public function editLecturer($id)
  {
      $this->generateCsrfToken();
      $departments = (new Department(PDO()))->getAllDepartments();
      $user = (new User(PDO()))->where('id', $id);
      if (!$user || $user->id === -1) {
          $this->sendNotFound();
      }

      // Kiểm tra quyền truy cập
      $currentUser = AUTHGUARD()->user();
      $isAdmin = $currentUser->role === 'admin';

      $this->sendPage('auth/lecturer/edit', [
          'lecturer' => $user,
          'is_admin' => $isAdmin,
          'departments' => $departments,
          'errors' => session_get_once('errors')
      ]);
  }

  public function updateStudent($id)
  {
      // Kiểm tra CSRF token
      $this->validateCsrfToken($_POST['csrf_token']);

      // Tìm sinh viên theo ID
      $user = (new User(PDO()))->where('id', $id);
      if (!$user || $user->id === -1) {
          $this->sendNotFound();
      }

      // Lấy toàn bộ dữ liệu từ form
      $data = $_POST;
      $data['role'] = $user->role;
      $majorModel = new Major(PDO());
      $major = $majorModel->find((int)$data['major_id']);

      // Lấy mã khoa từ bảng Department
      $deptModel = new Department(PDO());
      $department = $deptModel->find($major->department_id);
      $data['department_id'] = $department->id;

      $courseModel = new Course(PDO());
      $course = $courseModel->find($data['course_id']);
      $courseName = $course->courseName;

      $userModel = new User(PDO());
      $studentCount = $userModel->countStudentsByMajorDeptCourse(
          (int)$data['major_id'],
          $data['department_id'],
          (int)$data['course_id']
      );

      $label = 'A' . ceil(($studentCount + 1) / 60);
      $data['class'] = $department->departmentCode . $courseName . $major->majorCode . $label;
      $data['is_locked'] = $user->is_locked;

      // Gọi validateEdit để kiểm tra dữ liệu
      $model_errors = $user->validateEdit($data);

      // Nếu không có lỗi, tiến hành cập nhật
      if (empty($model_errors)) {
          $user->fill($data);
          $user->save();
          if(AUTHGUARD()->user()->role === 'admin'){
            $messages = ['success' => 'Cập nhật thông tin sinh viên thành công.'];
            redirect('/student', ['messages' => $messages]);
          }
          redirect('/home');
      }

      // Nếu có lỗi, lưu lại dữ liệu form và chuyển hướng lại trang sửa
      redirect('/student/edit/' . $id, [
          'errors' => $model_errors
      ]);
  }

  public function updateLecturer($id)
  {
      // Kiểm tra CSRF token
      $this->validateCsrfToken($_POST['csrf_token']);

      $user = (new User(PDO()))->where('id', $id);
      if (!$user || $user->id === -1) {
          $this->sendNotFound();
      }

      // Lấy toàn bộ dữ liệu từ form
      $data = $_POST;
      $data['role'] = $user->role;
      $data['major_id'] = $user->major_id;
      $data['course'] = $user->course_id;
      $data['is_locked'] = $user->is_locked;

      // Gọi validateEdit để kiểm tra dữ liệu
      $model_errors = $user->validateEditLecturer($data);

      // Nếu không có lỗi, tiến hành cập nhật
      if (empty($model_errors)) {
          $user->fill($data);
          $user->save();
          if(AUTHGUARD()->user()->role === 'admin'){
            $messages = ['success' => 'Cập nhật thông tin giảng viên thành công.'];
            redirect('/lecturer', ['messages' => $messages]);
          }
          redirect('/home');
      }

      // Nếu có lỗi, lưu lại dữ liệu form và chuyển hướng lại trang sửa
      redirect('/lecturer/edit/' . $id, [
          'errors' => $model_errors
      ]);
  }

  public function lock($id)
  {
      $this->generateCsrfToken();

      $user = (new User(PDO()))->where('id', $id);
      if (!$user) {
        $this->sendNotFound();
      }

      $user->lock();
      $messages = ['success' => 'Khóa tài khoản thành công.'];
      if($user->role === 'lecturer'){
        redirect('/lecturer', ['messages' => $messages]);
      }
      redirect('/student', ['messages' => $messages]);
  }

  public function unlock($id)
  {
      $this->generateCsrfToken();

      $user = (new User(PDO()))->where('id', $id);
      if (!$user) {
        $this->sendNotFound();
      }

      $user->unlock();
      $messages = ['success' => 'Mở khóa tài khoản thành công.'];
      if($user->role === 'lecturer'){
        redirect('/lecturer', ['messages' => $messages]);
      }
      redirect('/student', ['messages' => $messages]);
  }

  public function changePasswordForm()
  {
      $this->generateCsrfToken();
      $this->sendPage('auth/change_password', [
          'errors' => session_get_once('errors'),
          'messages' => session_get_once('messages')
      ]);
  }

  public function changePassword()
  {
      $this->validateCsrfToken($_POST['csrf_token']);

      $currentUser = AUTHGUARD()->user();
      $data = $_POST;

      $errors = [];

      // Kiểm tra mật khẩu cũ
      if (!password_verify($data['current_password'], $currentUser->password)) {
          $errors[0] = 'Mật khẩu hiện tại không chính xác.';
      }

      // Kiểm tra mật khẩu mới
      if (strlen($data['new_password']) < 6) {
          $errors[1] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
      }

      if ($data['new_password'] !== $data['confirm_password']) {
          $errors[2] = 'Xác nhận mật khẩu không khớp.';
      }

      if (!empty($errors)) {
          redirect('/change_password', ['errors' => $errors]);
      }

      // Cập nhật mật khẩu
      $currentUser->password = password_hash($data['new_password'], PASSWORD_DEFAULT);
      $currentUser->save();

      $messages = ['success' => 'Đổi mật khẩu thành công.'];
      redirect('/home', ['messages' => $messages]);
  }

}
