<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= $this->e($title) ?></title>

  <link rel="icon" href="./assets/img/icon.png?v=2" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/assets/css/style.css?v=1.8">
  <?= $this->section("page_specific_css") ?>
</head>

<body>
    <div class="header-banner d-flex justify-content-center align-items-center text-white text-center">
        <img src="/assets/img/footer.png" alt="Logo" class="me-3 logo-img">
        <h1 class="display-4 fw-bold">Hệ thống quản lý</h1>

        <?php if (AUTHGUARD()->isUserLoggedIn()) : ?>
        <div class="position-absolute" style="bottom: 10px; right: 10px;">
            <!-- Nút đăng xuất -->
            <a href="/logout" class="btn btn-light btn-sm d-block mb-2"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
            </a>
            <form id="logout-form" class="d-none" action="/logout" method="POST"></form>

            <!-- Nút trang chủ -->
            <a href="/" class="btn btn-light btn-sm d-block mb-2">
                <i class="bi bi-house-door-fill me-2"></i>Trang chủ
            </a>

            <!-- Nút đổi mật khẩu -->
            <a href="/change_password" class="btn btn-light btn-sm d-block">
                <i class="bi bi-key-fill me-2"></i>Đổi mật khẩu
            </a>
        </div>
        <?php endif ?>
    </div>

  <?= $this->section("page") ?>

  <hr>
  <footer class="text-light py-4" style="background-color: rgb(7, 15, 74); background-size: cover; background-position: center;">
      <div class="container d-flex justify-content-between">
          <div class="d-flex align-items-center">
              <a href="/home"><img src="/assets/img/footer.png" alt="Logo" style="width: 100px; height: auto;" class="me-3"></a>
              <div>
                  <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-geo-alt me-3"></i> <!-- Icon địa chỉ -->
                      <span>123 Đường Phố, TP.HCM</span>
                  </div>
                  <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-telephone me-3"></i> <!-- Icon số điện thoại -->
                      <span>+84 123 456 789</span>
                  </div>
                  <div class="d-flex align-items-center mt-2">
                      <i class="bi bi-envelope me-3"></i> <!-- Icon email -->
                      <span>info@example.com</span>
                  </div>
              </div>
          </div>
      </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous">
  </script>

  <?= $this->section("page_specific_js") ?>
</body>

</html>