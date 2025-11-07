<?php

namespace App\Controllers;

use App\Models\Course;

class CourseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function course()
    {
        $courses = (new Course(PDO()))->getAllCourses();
        $this->sendPage('course/course', [
            'courses' => $courses
        ]);
    }

    public function create()
    {
        $this->generateCsrfToken();

        $this->sendPage('course/create', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues()
        ]);
    }

    public function store()
    {
        $this->validateCsrfToken($_POST['csrf_token']);

        $data = [
            'courseName' => trim($_POST['courseName'])
        ];

        $course = new Course(PDO());
        $errors = $course->validate($data);

        if (empty($errors)) {
            $course->fill($data)->save();
            $messages = ['success' => 'Thêm khóa học thành công.'];
            redirect('/course', ['messages' => $messages]);
        }

        $this->saveFormValues($_POST);
        redirect('/course/create', ['errors' => $errors]);
    }

    public function edit($id)
    {
        $this->generateCsrfToken();

        $course = (new Course(PDO()))->find($id);
        if (!$course) {
            $this->sendNotFound();
        }

        $data = [
            'errors' => session_get_once('errors'),
            'course' => $course
        ];

        $this->sendPage('course/edit', $data);
    }

    public function update($id)
    {
        $this->validateCsrfToken($_POST['csrf_token']);

        $courseModel = new Course(PDO());
        $course = $courseModel->find($id);
        if (!$course) {
            $this->sendNotFound();
        }

        $data = [
            'courseName' => trim($_POST['courseName'] ?? '')
        ];

        $errors = $course->validateEdit($data, $course->courseName);

        if (empty($errors)) {
            $course->fill($data)->save();
            $messages = ['success' => 'Khóa học đã được cập nhật thành công.'];
            redirect('/course', ['messages' => $messages]);
        }

        redirect('/course/edit/' . $id, ['errors' => $errors]);
    }

    public function destroy($id)
    {
        $this->generateCsrfToken();

        $course = (new Course(PDO()))->find($id);
        if (!$course) {
            $this->sendNotFound();
        }

        $deleted = $course->delete();
        if (!$deleted) {
            $messages = ['error' => 'Không thể xóa khóa học vì có dữ liệu phụ thuộc.'];
            redirect('/course', ['messages' => $messages]);
        }

        $messages = ['success' => 'Khóa học đã được xóa thành công.'];
        redirect('/course', ['messages' => $messages]);
    }
}
