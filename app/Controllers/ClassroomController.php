<?php

namespace App\Controllers;

use App\Models\Classroom;

class ClassroomController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function classroom()
    {
        $classrooms = (new Classroom(PDO()))->getAllClassrooms();
        $this->sendPage('classroom/classroom', [
            'classrooms' => $classrooms
        ]);
    }

    public function create()
    {
        $this->generateCsrfToken();

        $this->sendPage('classroom/create', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues()
        ]);
    }

    public function store()
    {
        $this->validateCsrfToken($_POST['csrf_token']);

        $data = [
            'classroomName' => trim($_POST['classroomName'] ?? ''),
            'capacity' => $_POST['capacity'] ?? 0
        ];

        $classroom = new Classroom(PDO());
        $errors = $classroom->validate($data);

        if (empty($errors)) {
            $classroom->fill($data)->save();
            $messages = ['success' => 'Thêm phòng học thành công.'];
            redirect('/classroom', ['messages' => $messages]);
        }

        $this->saveFormValues($_POST);
        redirect('/classroom/create', ['errors' => $errors]);
    }

    public function edit($id)
    {
        $this->generateCsrfToken();

        $classroom = (new Classroom(PDO()))->find($id);
        if (!$classroom) {
            $this->sendNotFound();
        }

        $data = [
            'errors' => session_get_once('errors'),
            'classroom' => $classroom,
            'old' => $this->getSavedFormValues()
        ];

        $this->sendPage('classroom/edit', $data);
    }

    public function update($id)
    {
        $this->validateCsrfToken($_POST['csrf_token']);

        $classroomModel = new Classroom(PDO());
        $classroom = $classroomModel->find($id);
        if (!$classroom) {
            $this->sendNotFound();
        }

        // Kiểm tra xem phòng học có đang được dùng trong phân công giảng dạy không
        $stmt = PDO()->prepare('SELECT COUNT(*) FROM teaching_assignments WHERE classroom_id = :id');
        $stmt->execute(['id' => $id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $messages = ['error' => 'Không thể chỉnh sửa vì phòng học đang được sử dụng trong phân công giảng dạy.'];
            redirect('/classroom', ['messages' => $messages]);
        }

        $data = [
            'classroomName' => trim($_POST['classroomName'] ?? ''),
            'capacity' => $_POST['capacity'] ?? 0
        ];

        $errors = $classroom->validateEdit($data, $classroom->classroomName);

        if (empty($errors)) {
            $classroom->fill($data)->save();
            $messages = ['success' => 'Phòng học đã được cập nhật thành công.'];
            redirect('/classroom', ['messages' => $messages]);
        }

        redirect('/classroom/edit/' . $id, ['errors' => $errors]);
    }

    public function destroy($id)
    {
        $this->generateCsrfToken();

        $classroom = (new Classroom(PDO()))->find($id);
        if (!$classroom) {
            $this->sendNotFound();
        }

        $deleted = $classroom->delete();
        if (!$deleted) {
            $messages = ['error' => 'Không thể xóa phòng học vì có dữ liệu phụ thuộc.'];
            redirect('/classroom', ['messages' => $messages]);
        }

        $messages = ['success' => 'Phòng học đã được xóa thành công.'];
        redirect('/classroom', ['messages' => $messages]);
    }
}
