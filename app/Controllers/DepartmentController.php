<?php

namespace App\Controllers;

use App\Models\Department;

class DepartmentController extends Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function department()
  {
    $departments = (new Department(PDO()))->getAllDepartments();
    $this->sendPage('/department/department', [
      'departments' => $departments
    ]);
  }

  public function create()
  {
    $this->generateCsrfToken();

    $this->sendPage('department/create', [
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
        'departmentCode' => trim($_POST['departmentCode']),
        'departmentName' => trim($_POST['departmentName']),
    ];

    $newDepartment = new Department(PDO());

    // Xác thực dữ liệu
    $model_errors = $newDepartment->validate($data);

    if (empty($model_errors)) {
        $newDepartment->fill($data)->save();

        $messages = ['success' => 'Thêm khoa thành công.'];
        redirect('/department', ['messages' => $messages]);
    }

    // Nếu có lỗi thì lưu giá trị cũ và thông báo lỗi vào session
    $this->saveFormValues($_POST);
    redirect('/department/create', ['errors' => $model_errors]);
  }

  public function edit($id)
  {
    $this->generateCsrfToken();

    $department = (new Department(PDO()))->find($id);
    if (!$department) {
        $this->sendNotFound();
    }

    $form_values = $this->getSavedFormValues();
    $data = [
        'errors' => session_get_once('errors'),
        'department' => $department
    ];

    $this->sendPage('department/edit', $data);
  }

  public function update($id)
  {
    // Xác thực CSRF token
    $this->validateCsrfToken($_POST['csrf_token']);

    // Khởi tạo model và tìm department theo ID
    $departmentModel = new \App\Models\Department(PDO());
    $department = $departmentModel->find($id);

    if (!$department) {
        $this->sendNotFound();
    }

    // Lọc dữ liệu từ form
    $data = [
        'departmentCode' => trim($_POST['departmentCode'] ?? ''),
        'departmentName' => trim($_POST['departmentName'] ?? '')
    ];

    // Gán ID để loại trừ chính nó khi kiểm tra trùng lặp
    $data['id'] = $id;

    // Validate dữ liệu
    $errors = $department->validateEdit($data, $department->departmentCode, $department->departmentName);

    if (empty($errors)) {
        // Gán dữ liệu và lưu
        $department->fill($data);
        $department->save();

        $messages = ['success' => 'Khoa đã được cập nhật thành công.'];
        redirect('/department', ['messages' => $messages]);
    }

    redirect('/department/edit/' . $id, ['errors' => $errors]);
  }

  public function destroy($id)
  {
    $this->generateCsrfToken();

    // Tìm department theo id
    $department = (new Department(PDO()))->find($id);
    if (!$department) {
        $this->sendNotFound();
    }


    // Xóa department
    $deleteSuccess = $department->delete();

    if (!$deleteSuccess) {
        // Nếu có dữ liệu phụ thuộc, không cho phép xóa
        $messages = ['error' => 'Không thể xóa khoa vì có dữ liệu phụ thuộc.'];
        redirect('/department', ['messages' => $messages]);
    }

    // Thông báo thành công
    $messages = ['success' => 'Khoa đã được xóa thành công.'];
    redirect('/department', ['messages' => $messages]);
  }

}
