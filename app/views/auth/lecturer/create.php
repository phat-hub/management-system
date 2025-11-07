<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-6 offset-md-3 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Thêm tài khoản giảng viên</h2>
          <form class="row g-3" method="post" action="/lecturer/register">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Mã số giảng viên -->
            <div class="form-group col-12">
              <label for="peopleId" class="fw-bold">Mã số giảng viên:</label>
              <input 
                type="text" 
                id="peopleId" 
                name="peopleId" 
                class="form-control <?= isset($errors['peopleId']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập mã số giảng viên"
                value="<?= $this->e($old['peopleId'] ?? '') ?>"
              >
              <?php if (isset($errors['peopleId'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['peopleId']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Tên -->
            <div class="form-group col-12">
              <label for="name" class="fw-bold">Tên giảng viên:</label>
              <input 
                type="text" 
                id="name" 
                name="name" 
                class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập tên giảng viên"
                value="<?= $this->e($old['name'] ?? '') ?>"
              >
              <?php if (isset($errors['name'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['name']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Giới tính -->
            <div class="form-group col-12">
              <label for="gender" class="fw-bold">Giới tính:</label>
              <select 
                id="gender" 
                name="gender" 
                class="form-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>"
              >
                <option value="">-- Chọn giới tính --</option>
                <option value="Nam" <?= ($old['gender'] ?? '') === 'Nam' ? 'selected' : '' ?>>Nam</option>
                <option value="Nữ" <?= ($old['gender'] ?? '') === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
              </select>
              <?php if (isset($errors['gender'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['gender']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Khoa -->
            <div class="form-group col-12">
              <label for="department_id" class="fw-bold">Khoa:</label>
              <select 
                id="department_id" 
                name="department_id" 
                class="form-select <?= isset($errors['department_id']) ? 'is-invalid' : '' ?>"
              >
                <option value="">-- Chọn khoa --</option>
                <?php foreach ($departments as $department) : ?>
                  <option 
                    value="<?= $department->id ?>"
                    <?= ($old['department_id'] ?? '') == $department->id ? 'selected' : '' ?>
                  >
                    <?= $this->e($department->departmentName) ?>
                  </option>
                <?php endforeach ?>
              </select>
              <?php if (isset($errors['department_id'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['department_id']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Mật khẩu -->
            <div class="form-group col-12">
              <label for="password" class="fw-bold">Mật khẩu:</label>
              <input 
                type="password" 
                id="password" 
                name="password" 
                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập mật khẩu"
              >
              <?php if (isset($errors['password'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['password']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="form-group col-12">
              <label for="password_confirmation" class="fw-bold">Xác nhận mật khẩu:</label>
              <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                class="form-control" 
                placeholder="Nhập lại mật khẩu"
              >
            </div>

            <!-- Nút hành động -->
            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary px-4">Tạo tài khoản</button>
              <a href="/lecturer" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>
