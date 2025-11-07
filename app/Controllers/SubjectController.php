<?php

namespace App\Controllers;

use App\Models\Subject;

class SubjectController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function subject()
    {
        $subjects = (new Subject(PDO()))->getAllSubjects();
        $this->sendPage('/subject/subject', [
            'subjects' => $subjects
        ]);
    }

    public function create()
    {
        $this->generateCsrfToken();

        $this->sendPage('subject/create', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues()
        ]);
    }

    public function store()
    {
        $this->validateCsrfToken($_POST['csrf_token']);

        $data = [
            'subjectCode' => trim($_POST['subjectCode']),
            'subjectName' => trim($_POST['subjectName']),
            'credits' => trim($_POST['credits'])
        ];

        $newSubject = new Subject(PDO());
        $errors = $newSubject->validate($data);

        if (empty($errors)) {
            $newSubject->fill($data)->save();
            $messages = ['success' => 'Thêm học phần thành công.'];
            redirect('/subject', ['messages' => $messages]);
        }

        $this->saveFormValues($_POST);
        redirect('/subject/create', ['errors' => $errors]);
    }

    public function edit($id)
    {
        $this->generateCsrfToken();

        $subject = (new Subject(PDO()))->find($id);
        if (!$subject) {
            $this->sendNotFound();
        }

        $this->sendPage('subject/edit', [
            'subject' => $subject,
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues()
        ]);
    }

    public function update($id)
    {
        $this->validateCsrfToken($_POST['csrf_token']);

        $subjectModel = new Subject(PDO());
        $subject = $subjectModel->find($id);

        if (!$subject) {
            $this->sendNotFound();
        }

        $data = [
            'subjectCode' => trim($_POST['subjectCode']),
            'subjectName' => trim($_POST['subjectName']),
            'credits' => trim($_POST['credits'])
        ];

        $errors = $subject->validateEdit($data, $subject->subjectCode, $subject->subjectName);

        if (empty($errors)) {
            $subject->fill($data)->save();
            $messages = ['success' => 'Cập nhật học phần thành công.'];
            redirect('/subject', ['messages' => $messages]);
        }

        redirect('/subject/edit/' . $id, ['errors' => $errors]);
    }

    public function destroy($id)
    {
        $this->generateCsrfToken();

        $subject = (new Subject(PDO()))->find($id);
        if (!$subject) {
            $this->sendNotFound();
        }

        $deleted = $subject->delete();

        if (!$deleted) {
            $messages = ['error' => 'Không thể xóa học phần vì có dữ liệu phụ thuộc.'];
            redirect('/subject', ['messages' => $messages]);
        }

        $messages = ['success' => 'Xóa học phần thành công.'];
        redirect('/subject', ['messages' => $messages]);
    }
}
