<?php

namespace App\Controllers;

use App\Models\Major;
use App\Models\Department;

class MajorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function major()
    {
        // Lấy tất cả các ngành học
        $majors = (new Major(PDO()))->getAllMajors();
        
        // Gửi dữ liệu tới trang major
        $this->sendPage('/major/major', [
            'majors' => $majors
        ]);
    }

    public function create()
    {
        $this->generateCsrfToken();

        $departments = (new Department(PDO()))->getAllDepartments();

        $this->sendPage('major/create', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues(),
            'departments' => $departments
        ]);
    }

    public function store()
    {
        // Xác thực CSRF token
        $this->validateCsrfToken($_POST['csrf_token']);

        // Lọc dữ liệu đầu vào
        $data = [
            'majorCode' => trim($_POST['majorCode'] ?? ''),
            'majorName' => trim($_POST['majorName'] ?? ''),
            'department_id' => isset($_POST['department_id']) ? (int)$_POST['department_id'] : null,
        ];

        $newMajor = new Major(PDO());

        // Xác thực dữ liệu
        $model_errors = $newMajor->validate($data);

        if (empty($model_errors)) {
            $newMajor->fill($data)->save();

            $messages = ['success' => 'Thêm ngành thành công.'];
            redirect('/major', ['messages' => $messages]);
        }

        // Nếu có lỗi thì lưu lại dữ liệu cũ và báo lỗi
        $this->saveFormValues($_POST);
        redirect('/major/create', ['errors' => $model_errors]);
    }

    public function edit($id)
    {
        $this->generateCsrfToken();

        // Tìm ngành theo ID
        $major = (new Major(PDO()))->find($id);
        if (!$major) {
            $this->sendNotFound();  // Nếu không tìm thấy ngành, trả về lỗi 404
        }


        // Lấy danh sách các khoa để hiển thị trong select
        $departments = (new Department(PDO()))->getAllDepartments();

        // Chuẩn bị dữ liệu để gửi tới view
        $data = [
            'errors' => session_get_once('errors'), // Các lỗi (nếu có)
            'major' => $major, // Thông tin ngành hiện tại
            'departments' => $departments // Danh sách các khoa
        ];

        // Gửi trang chỉnh sửa ngành
        $this->sendPage('major/edit', $data);
    }

    public function update($id)
    {
        // Xác thực CSRF token
        $this->validateCsrfToken($_POST['csrf_token']);

        // Khởi tạo model và tìm major theo ID
        $majorModel = new \App\Models\Major(PDO());
        $major = $majorModel->find($id);

        if (!$major) {
            $this->sendNotFound();
        }

        // Lọc dữ liệu từ form
        $data = [
            'majorCode' => trim($_POST['majorCode'] ?? ''),
            'majorName' => trim($_POST['majorName'] ?? ''),
            'department_id' => $_POST['department_id'] ?? null
        ];

        // Gọi hàm validateEdit để kiểm tra dữ liệu
        $errors = $major->validateEdit($data, $major->majorName, $major->id);

        if (empty($errors)) {
            // Gán dữ liệu và lưu
            $major->fill($data);
            $major->save();

            $messages = ['success' => 'Ngành đã được cập nhật thành công.'];
            redirect('/major', ['messages' => $messages]);
        }

        redirect('/major/edit/' . $id, ['errors' => $errors]);
    }

    public function destroy($id)
    {
        $this->generateCsrfToken();

        // Tìm major theo id
        $major = (new Major(PDO()))->find($id);
        if (!$major) {
            $this->sendNotFound();
        }


        // Xóa major
        $deleteSuccess = $major->delete();

        if (!$deleteSuccess) {
            // Nếu có dữ liệu phụ thuộc, không cho phép xóa
            $messages = ['error' => 'Không thể xóa ngành vì có dữ liệu phụ thuộc.'];
            redirect('/major', ['messages' => $messages]);
        }

        // Thông báo thành công
        $messages = ['success' => 'Ngành đã được xóa thành công.'];
        redirect('/major', ['messages' => $messages]);
    }


}
