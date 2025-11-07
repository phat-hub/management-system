<?php

namespace App\Controllers;

use App\Models\Semester;
use App\Models\RegistrationStatus;

class SemesterController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function semester()
    {
        $semesters = (new Semester(PDO()))->getAllSemesters();
        $this->sendPage('/semester/semester', [
            'semesters' => $semesters
        ]);
    }

    public function create()
    {
        $this->generateCsrfToken();

        $this->sendPage('semester/create', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues()
        ]);
    }

    public function store()
    {
        // Xác thực CSRF token
        $this->validateCsrfToken($_POST['csrf_token']);

        // Lọc dữ liệu đầu vào
        $data = [
            'semester' => trim($_POST['semester']),
            'academicYear' => trim($_POST['academicYear']),
        ];

        $newSemester = new Semester(PDO());

        // Xác thực dữ liệu
        $model_errors = $newSemester->validate($data);

        if (empty($model_errors)) {
            $newSemester->fill($data)->save();
            if((new RegistrationStatus(PDO()))->count() === 0) (new RegistrationStatus(PDO()))->save();
            else (new RegistrationStatus(PDO()))->close();
            $messages = ['success' => 'Thêm học kỳ thành công.'];
            redirect('/semester', ['messages' => $messages]);
        }

        // Nếu có lỗi thì lưu giá trị cũ và thông báo lỗi vào session
        $this->saveFormValues($_POST);
        redirect('/semester/create', ['errors' => $model_errors]);
    }

    public function edit($id)
    {
        $this->generateCsrfToken();

        $semester = (new Semester(PDO()))->find($id);
        if (!$semester) {
            $this->sendNotFound();
        }

        $data = [
            'errors' => session_get_once('errors'),
            'semester' => $semester
        ];

        $this->sendPage('semester/edit', $data);
    }

    public function update($id)
    {
        // Xác thực CSRF token
        $this->validateCsrfToken($_POST['csrf_token']);

        // Khởi tạo model và tìm semester theo ID
        $semesterModel = new Semester(PDO());
        $semester = $semesterModel->find($id);

        if (!$semester) {
            $this->sendNotFound();
        }

        // Lọc dữ liệu từ form
        $data = [
            'semester' => trim($_POST['semester'] ?? ''),
            'academicYear' => trim($_POST['academicYear'] ?? '')
        ];

        // Gán ID để loại trừ chính nó khi kiểm tra trùng lặp
        $data['id'] = $id;

        // Validate dữ liệu
        $errors = $semester->validateEdit($data, $semester->semester, $semester->academicYear);

        if (empty($errors)) {
            // Gán dữ liệu và lưu
            $semester->fill($data);
            $semester->save();

            $messages = ['success' => 'Học kỳ đã được cập nhật thành công.'];
            redirect('/semester', ['messages' => $messages]);
        }

        redirect('/semester/edit/' . $id, ['errors' => $errors]);
    }

    public function destroy($id)
    {
        $this->generateCsrfToken();

        // Tìm semester theo id
        $semester = (new Semester(PDO()))->find($id);
        if (!$semester) {
            $this->sendNotFound();
        }

        // Xóa semester
        $deleteSuccess = $semester->delete();

        if (!$deleteSuccess) {
            // Nếu có dữ liệu phụ thuộc, không cho phép xóa
            $messages = ['error' => 'Không thể xóa học kỳ vì có dữ liệu phụ thuộc.'];
            redirect('/semester', ['messages' => $messages]);
        }

        // Thông báo thành công
        $messages = ['success' => 'Học kỳ đã được xóa thành công.'];
        redirect('/semester', ['messages' => $messages]);
    }
}
