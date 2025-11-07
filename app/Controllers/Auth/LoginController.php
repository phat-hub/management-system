<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;



class LoginController extends Controller
{
  public function create()
  {
    if (AUTHGUARD()->isUserLoggedIn()) {
      redirect('/home');
    }

    $this->generateCsrfToken();

    $data = [
      'messages' => session_get_once('messages'),
      'old' => $this->getSavedFormValues(),
      'errors' => session_get_once('errors')
    ];

    $this->sendPage('auth/login', $data);
  }

  public function store()
  {
    // Xác thực CSRF token
    $this->validateCsrfToken($_POST['csrf_token']);

    $user_credentials = $this->filterUserCredentials($_POST);

    $errors = [];
    $user = (new User(PDO()))->where('peopleId', $user_credentials['peopleId']);

    // Kiểm tra xem user có tồn tại không
    if ($user->id === -1) {
        $errors['peopleId'] = 'Mã số đăng nhập hoặc mật khẩu không hợp lệ.';
    } else if ($user->is_locked) {
        // Tài khoản bị khóa
        $messages = ['error' => 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.'];
        redirect('/login', ['messages' => $messages]);
    } else if (AUTHGUARD()->login($user, $user_credentials)) {
        // Đăng nhập thành công
        redirect('/home');
    } else {
        // Sai mật khẩu
        $errors['password'] = 'Mã số đăng nhập hoặc mật khẩu không hợp lệ.';
    }

    // Lưu lại dữ liệu trừ password để hiện lại trên form
    $this->saveFormValues($_POST, ['password']);
    redirect('/login', ['errors' => $errors]);
  }


  public function destroy()
  {
    AUTHGUARD()->logout();
    redirect('/login');
  }

  protected function filterUserCredentials(array $data)
  {
    return [
      'peopleId' => $data['peopleId'] ?? null,
      'password' => $data['password'] ?? null
    ];
  }
}
