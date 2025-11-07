<?php

namespace App\Controllers;

use League\Plates\Engine;

class Controller
{
    protected $view;

    public function __construct()
    {
        $this->view = new Engine(ROOTDIR . 'app/views');
    }

    // Phương thức tạo CSRF token nếu chưa có
    protected function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Tạo token ngẫu nhiên
        }
    }
    // Phương thức xác thực CSRF token khi gửi form
    protected function validateCsrfToken($token)
    {
        if (!isset($token) || $token !== $_SESSION['csrf_token']) {
            die('CSRF token mismatch'); // Nếu token không hợp lệ, dừng lại
        }
    }

    public function sendPage($page, array $data = [])
    {
        // Escape từng phần tử trong mảng $data
        $data = array_map(function($value) {
            return is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
        }, $data);

        exit($this->view->render($page, $data)); // Render trang với token và dữ liệu đã được escape
    }


    // Lưu các giá trị của form vào $_SESSION
    protected function saveFormValues(array $data, array $except = [])
    {
        $form = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, $except, true)) {
                $form[$key] = $value;
            }
        }
        $_SESSION['form'] = $form;
    }

    protected function getSavedFormValues()
    {
        return session_get_once('form', []);
    }

    public function sendNotFound()
    {
        http_response_code(404);
        exit($this->view->render('errors/404'));
    }
}
