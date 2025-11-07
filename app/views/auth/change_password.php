<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-6 offset-md-3 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Đổi mật khẩu</h2>
          <form class="row g-3" method="post" action="/change_password">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Mật khẩu hiện tại -->
            <div class="form-group col-12">
              <label for="current_password" class="fw-bold">Mật khẩu hiện tại:</label>
              <input 
                type="password" 
                id="current_password" 
                name="current_password" 
                class="form-control <?= isset($errors[0]) && str_contains($errors[0], 'hiện tại') ? 'is-invalid' : '' ?>" 
                placeholder="Nhập mật khẩu hiện tại"
              >
              <?php if (isset($errors[0]) && str_contains($errors[0], 'hiện tại')) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors[0]) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Mật khẩu mới -->
            <div class="form-group col-12">
              <label for="new_password" class="fw-bold">Mật khẩu mới:</label>
              <input 
                type="password" 
                id="new_password" 
                name="new_password" 
                class="form-control <?= isset($errors[1]) && str_contains($errors[1], 'ít nhất') ? 'is-invalid' : '' ?>" 
                placeholder="Nhập mật khẩu mới"
              >
              <?php if (isset($errors[1]) && str_contains($errors[1], 'ít nhất')) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors[1]) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Nhập lại mật khẩu mới -->
            <div class="form-group col-12">
              <label for="confirm_password" class="fw-bold">Nhập lại mật khẩu mới:</label>
              <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                class="form-control <?= isset($errors[2]) && str_contains($errors[2], 'khớp') ? 'is-invalid' : '' ?>" 
                placeholder="Nhập lại mật khẩu mới"
              >
              <?php if (isset($errors[2]) && str_contains($errors[2], 'khớp')) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors[2]) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Nút submit -->
            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary px-4">Đổi mật khẩu</button>
              <a href="/home" class="btn btn-secondary ms-2">Quay lại</a>
            </div>

            <!-- Thông báo thành công -->
            <?php if (!empty($messages['success'])) : ?>
              <div class="alert alert-success mt-3 text-center">
                <?= $this->e($messages['success']) ?>
              </div>
            <?php endif ?>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>
